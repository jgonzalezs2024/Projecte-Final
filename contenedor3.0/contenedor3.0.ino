// ===========================
// DEFINICIÓN DE LIBRERÍAS //
// ===========================

#include <TinyGPS++.h>       // Librería para manejar GPS
#include <SPI.h>             // Librería para comunicación SPI
#include <MFRC522.h>         // Librería para el lector RFID RC522
#include "HX711.h"           // Librería para la celda de carga

// ============================
// DEFINICIÓN DE PINES //
// ============================

#define RST_PIN     5        // Pin de reset del lector RFID
#define SS_PIN      53       // Pin de selección del lector RFID
#define RGB_GREEN   3        // Pin para LED RGB verde
#define RGB_BLUE    2        // Pin para LED RGB azul
#define RGB_RED     7        // Pin para LED RGB rojo

const int DOUT     = A1;     // Pin de datos de la celda de carga
const int CLK      = A0;     // Pin de reloj de la celda de carga

const int PinIN1   = 35;     // Control del motor IN1
const int PinIN2   = 37;     // Control del motor IN2
const int boton    = 27;     // Botón

const int trigPin  = 9;      // Trigger del sensor ultrasónico 1
const int echoPin  = 10;     // Echo del sensor ultrasónico 1
const int trigPin2 = 11;     // Trigger del sensor ultrasónico 2
const int echoPin2 = 12;     // Echo del sensor ultrasónico 2

// ===========================
// VARIABLES GLOBALES //
// ===========================

bool uidValido;              // Bandera para saber si la tarjeta RFID es válida
bool activo;                 // Estado general del contenedor
bool tieneCoordenadas;       // Bandera para saber si se han recibido coordenadas
bool puertaAbierta;          // Bandera para estado de la puerta

float pes;                   // Peso leído por la balanza
float distance, distance2;   // Distancias medidas por los sensores ultrasónicos

int control;                 // Variable de control genérica

long duration, duration2;    // Duraciones de los pulsos ultrasónicos

double lat, lng;             // Coordenadas GPS

char c;                      // Carácter leído en GPS

String peticio;              // Petición generada
String resultat;             // Resultado de una petición o procesamiento

byte ActualUID[4];           // Almacena el UID actual leído del RFID

// ===========================
// INSTANCIAS DE OBJETOS //
// ===========================

HX711 balanza;                        // Objeto para la balanza
MFRC522 mfrc522(SS_PIN, RST_PIN);     // Objeto para el lector RFID
TinyGPSPlus gps;                      // Objeto para el GPS
String id_container = "1";           // ID del contenedor

void setup() {
  // ===============================
  // CONFIGURACIÓN DE PINES
  // ===============================
  pinMode(boton, INPUT_PULLUP);     // Botón con resistencia pull-up
  pinMode(PinIN1, OUTPUT);          // Motor IN1
  pinMode(PinIN2, OUTPUT);          // Motor IN2

  pinMode(RGB_RED, OUTPUT);         // LED RGB - Rojo
  pinMode(RGB_GREEN, OUTPUT);       // LED RGB - Verde
  pinMode(RGB_BLUE, OUTPUT);        // LED RGB - Azul

  pinMode(trigPin, OUTPUT);         // Trigger sensor ultrasónico 1
  pinMode(echoPin, INPUT);          // Echo sensor ultrasónico 1

  pinMode(trigPin2, OUTPUT);        // Trigger sensor ultrasónico 2
  pinMode(echoPin2, INPUT);         // Echo sensor ultrasónico 2

  // ===============================
  // INICIALIZACIÓN DE COMUNICACIONES
  // ===============================
  Serial.begin(115200);             // Monitor serie
  Serial1.begin(9600);              // GPS por puerto Serial1
  Serial2.begin(9600);              // Puerto adicional (por ejemplo, WiFi/GSM)

  delay(1000);                      // Espera para estabilizar

  // ===============================
  // INICIALIZACIÓN DE MÓDULOS
  // ===============================
  SPI.begin();                      // Bus SPI para RFID
  mfrc522.PCD_Init();               // Inicializar módulo RFID

  balanza.begin(DOUT, CLK);         // Inicializar balanza (HX711)
  balanza.set_scale(-40000);       // Establecer escala personalizada
  balanza.tare(20);                // Tarear con 20 lecturas

  // ===============================
  // OBTENER COORDENADAS GPS INICIALES
  // ===============================
  tieneCoordenadas = false;
  while (Serial1.available()) {
    c = Serial1.read();
    gps.encode(c);

    if (gps.location.isUpdated()) {
      tieneCoordenadas = true;

      lat = gps.location.lat();
      lng = gps.location.lng();

      peticio = "?lat=" + String(lat) + "&lng=" + String(lng) + "&id_container=" + id_container;
      resultat = enviar_i_rebre_dades(peticio);
      Serial.println(resultat);

      control = resultat.toInt();
      if (control == -1) {
        Serial.println("FALLO EN EL INSERT LAT LONG");
      } else if (control == 1) {
        Serial.println("REGISTRO ACTUALIZADO LAT LONG");
      }
    }
  }

  // ===============================
  // COMPROBACIÓN DE ACTIVACIÓN
  // ===============================
  peticio = "?comprovacio=1&id_container=" + id_container;
  resultat = enviar_i_rebre_dades(peticio);
  resultat.trim();
  Serial.println(resultat);

  if (resultat == "f") {
    // Acceso denegado
    Serial.println("VARIABLE FALSE");
    activo = false;
    digitalWrite(RGB_GREEN, HIGH); 
    digitalWrite(RGB_BLUE, HIGH); 
    digitalWrite(RGB_RED, LOW);
  } else if (resultat == "t") {
    // Acceso permitido
    Serial.println("VARIABLE TRUE");
    activo = true;
    digitalWrite(RGB_GREEN, LOW); 
    digitalWrite(RGB_BLUE, HIGH); 
    digitalWrite(RGB_RED, HIGH);
  }
}
void loop() {

  // ===============================
  // SENSOR ULTRASÓNICO 1
  // ===============================
  digitalWrite(trigPin, LOW);
  delayMicroseconds(2);
  digitalWrite(trigPin, HIGH);
  delayMicroseconds(10);
  digitalWrite(trigPin, LOW);
  duration = pulseIn(echoPin, HIGH);
  distance = duration * 0.034 / 2;

  // ===============================
  // SENSOR ULTRASÓNICO 2
  // ===============================
  digitalWrite(trigPin2, LOW);
  delayMicroseconds(2);
  digitalWrite(trigPin2, HIGH);
  delayMicroseconds(10);
  digitalWrite(trigPin2, LOW);
  duration2 = pulseIn(echoPin2, HIGH);
  distance2 = duration2 * 0.034 / 2;

  // ===============================
  // SI DETECTA MAS DE DE 10CM, CONTENEDOR ACTIVO
  // ===============================
  if (distance >= 10.00 || distance2 >= 10.00) {
    digitalWrite(RGB_GREEN, LOW); 
    digitalWrite(RGB_BLUE, HIGH); 
    digitalWrite(RGB_RED, HIGH);
    delay(2000);

    if (activo != true) {
      peticio = "?activo=1&id_container=" + id_container;
      resultat = enviar_i_rebre_dades(peticio);
      control = resultat.toInt();

      if (control == -1) {
        Serial.println("FALLO EL CAMBIO DE ESTADO");
      } else if (control == 1) {
        Serial.println("REGISTRO ACTUALIZADO");
        activo = true;
      }
    }

    Serial.println("CONTENEDOR DISPONIBLE");
    Serial.println("Lectura i accés del UID");
    delay(2000);

    // ===============================
    // LECTURA RFID
    // ===============================
    uidValido = false;
    while (uidValido == false) {
      if (mfrc522.PICC_IsNewCardPresent()) {
        if (mfrc522.PICC_ReadCardSerial()) {
          String uidString = "?rfid=";
          for (byte i = 0; i < mfrc522.uid.size; i++) {
            if (mfrc522.uid.uidByte[i] < 0x10) {
              uidString += " 0";
            } else {
              uidString += "";
            }
            uidString += String(mfrc522.uid.uidByte[i], HEX);
          }
          peticio = uidString;
          uidValido = true;
          mfrc522.PICC_HaltA();
        }
      }
    }

    resultat = enviar_i_rebre_dades(peticio);
    control = resultat.toInt();

    if (control == -1) {
      // ACCESO DENEGADO
      Serial.println("TARJETA DENEGADA");
      digitalWrite(RGB_GREEN, HIGH); 
      digitalWrite(RGB_BLUE, LOW); 
      digitalWrite(RGB_RED, HIGH);
      delay(5000);
    } else if (control == 0) {
      Serial.println("ERROR");
    } else if (control == 1) {
      // ===============================
      // TAPA ABIERTA Y ESPERA DE BOTÓN
      // ===============================
      levantarTapa();
      delay(20000);
      Serial.println("PUERTA ABIERTA");
      puertaAbierta = true;

      while (puertaAbierta) {
        if (digitalRead(boton) == LOW) {
          cerrarTapa();
          delay(20000);
          Serial.println("PUERTA CERRADA");

          puertaAbierta = false;
        }
        delay(50);
      }

      pararMotor();
      Serial.println("Motor Detenido");
      delay(1000);

      // ===============================
      // ENVIAR PESO Y ACTUALIZAR
      // ===============================
      peticio += "&pes=";
      pes = balanza.get_units(20);
      peticio += String(pes, 2);
      peticio += "&id_container=" + id_container;
      enviar_i_rebre_dades(peticio);

      control = resultat.toInt();
      if (control == -1) {
        Serial.println("REGISTRO DENEGADO");
      } else if (control == 1) {
        Serial.println("REGISTRO COMPLETADO");
      }
      delay(500);
    }

  } else {
    // ===============================
    // SI DETECTA MENOS DE DE 10CM, CONTENEDOR INACTIVO
    // ===============================
    digitalWrite(RGB_GREEN, HIGH); 
    digitalWrite(RGB_BLUE, HIGH); 
    digitalWrite(RGB_RED, LOW);
    Serial.println("Contenedor lleno");

    // ===============================
    // OBTENER COORDENADAS SI AÚN NO LAS TENEMOS
    // ===============================
    if (tieneCoordenadas != true) {
      while (Serial1.available()) {
        c = Serial1.read();
        gps.encode(c);

        if (gps.location.isUpdated()) {
          tieneCoordenadas = true;

          lat = gps.location.lat();
          lng = gps.location.lng();
          peticio = "?lat=" + String(lat) + "&lng=" + String(lng) + "&id_container=" + id_container;
          resultat = enviar_i_rebre_dades(peticio);

          control = resultat.toInt();
          if (control == -1) {
            Serial.println("FALLO EN EL INSERT LAT LONG");
          } else if (control == 1) {
            Serial.println("REGISTRO ACTUALIZADO LAT LONG");
          }
        }
      }
    }

    delay(2000);

    // ===============================
    // ACTUALIZAR ESTADO A INACTIVO
    // ===============================
    if (activo != false) {
      peticio = "?activo=0&id_container=" + id_container;
      resultat = enviar_i_rebre_dades(peticio);
      control = resultat.toInt();

      if (control == -1) {
        Serial.println("FALLO EL CAMBIO DE ESTADO");
      } else if (control == 1) {
        Serial.println("REGISTRO ACTUALIZADO");
        activo = false;

        peticio = "?activo=0&id_container=" + id_container;
      }

      // ===============================
      // REGISTRAR VACIADO
      // ===============================
      peticio = "?id_container=" + id_container + "&lat=45.224152&lng=3.725570" + "&pes=" + String(pes, 2);
      resultat = enviar_i_rebre_dades(peticio);
      control = resultat.toInt();

      if (control == -1) {
        Serial.println("FALLO AL INSERTAR EN VACIADOS");
      } else if (control == 1) {
        Serial.println("REGISTRO EXITOSO EN VACIADOS");
      }
    }

    delay(5000);
  }
}
// ==============================================
// ENVÍA PETICIÓN A ESP32 Y RECIBE RESPUESTA
// ==============================================
String enviar_i_rebre_dades(String peticio) {
  Serial.println("Enviando UID a ESP32: " + peticio);
  Serial2.println(peticio);  // Enviar por Serial2
  delay(2000);               // Espera para respuesta

  resultat = "0";
  while (resultat == "0") {
    resultat = Serial2.readStringUntil('\n');  // Lee hasta nueva línea
  }
  return resultat;
}


// ==============================================
// CONTROL DE MOTOR - TAPA
// ==============================================

// LEVANTAR TAPA
void levantarTapa() {
  digitalWrite(PinIN1, HIGH);
  digitalWrite(PinIN2, LOW);
}

// CERRAR TAPA
void cerrarTapa() {
  digitalWrite(PinIN1, LOW);
  digitalWrite(PinIN2, HIGH);
}

// PARAR MOTOR
void pararMotor() {
  digitalWrite(PinIN1, LOW);
  digitalWrite(PinIN2, LOW);
}
