#include <TinyGPS++.h>
#include <SoftwareSerial.h>
#include <SPI.h>
#include <MFRC522.h>
#include "HX711.h"

#define RST_PIN 5           // Pin número 5 para el RST del RC522
#define SS_PIN 53           // Pin número 53 para el SS (SDA) del RC522
#define RGB_GREEN 3
#define RGB_BLUE 2
#define RGB_RED 6
#define GPS_RX 16  // Conectar al TX del GPS
#define GPS_TX 17  // (opcional, normalmente no se usa)

int control;
const int DOUT = A1;
const int CLK = A0;
HX711 balanza;
String id_container = "1";
const int boton = 24;       // Pin para el botón
const int PinIN1 = 22;     // Pin para controlar el motor (nuevo)
const int PinIN2 = 23;     // Pin para controlar el motor (nuevo)
const unsigned long tiempoLimite = 5 * 60 * 1000; // 5 minutos en milisegundos (300000ms)
unsigned long lastGpsAttempt = 0;
unsigned long tiempoInicio; // Variable para almacenar el tiempo de inicio
SoftwareSerial gpsSerial(GPS_RX, GPS_TX);
TinyGPSPlus gps;
MFRC522 mfrc522(SS_PIN, RST_PIN); // Creamos el objeto para el RC522
byte ActualUID[4];               // Almacena el código único leído de la tarjeta
String peticio, resultat, consulta;
bool prova, activo;
const int trigPin = 9;
const int echoPin = 10;
const int trigPin2 = 11;
const int echoPin2 = 12;
float pes;

void setup() {
  pinMode(boton, INPUT_PULLUP);    // Configuramos el botón como entrada con pullup
  pinMode(PinIN1, OUTPUT);         // Configuramos el pin del motor como salida
  pinMode(PinIN2, OUTPUT);         // Configuramos el pin del motor como salida
  pinMode(RGB_RED, OUTPUT);
  pinMode(RGB_GREEN, OUTPUT);
  pinMode(RGB_BLUE, OUTPUT);
  pinMode(trigPin, OUTPUT);        // Define TRIG como salida
  pinMode(echoPin, INPUT);
  pinMode(trigPin2, OUTPUT);
  pinMode(echoPin2, INPUT);
  
  Serial.begin(115200);
  gpsSerial.begin(9600);
  Serial2.begin(9600);             // TX2 = 16, RX2 = 17
  delay(1000);
  Serial.println("Arduino Mega listo.");
  
  SPI.begin();                    // Iniciamos el Bus SPI
  mfrc522.PCD_Init();             // Inicializamos el MFRC522
  
  balanza.begin(DOUT, CLK);
  Serial.print("Lectura del valor del ADC:  ");
  Serial.println(balanza.read());
  Serial.println("No ponga ningun objeto sobre la balanza");
  Serial.println("Destarando...");
  Serial.println("...");
  
  balanza.set_scale(12200);        // Establecemos la escala
  balanza.tare(20);                // El peso actual es considerado Tara.
  Serial.println("Listo para pesar");

  // Configuración GPS (con timeout de 5 segundos)
  unsigned long gpsTiempoInicio = millis();
  bool gpsValido = false;

  while (millis() - gpsTiempoInicio < 5000) {
    while (gpsSerial.available()) {
      char c = gpsSerial.read();
      gps.encode(c);

      if (gps.location.isUpdated()) {
        gpsValido = true;
        break;
      }
    }
    if (gpsValido) break;
  }

  if (gpsValido) {
    double lat = gps.location.lat();
    double lng = gps.location.lng();

    Serial.print("Latitud: ");
    Serial.println(lat, 6);
    Serial.print("Longitud: ");
    Serial.println(lng, 6);

    peticio = "?lat=" + String(lat, 6) + "&lng=" + String(lng, 6) + "&id_container=" + id_container;
  } else {
    Serial.println("No se obtuvieron coordenadas GPS");
    peticio = "?lat=NULL&lng=NULL&id_container=" + id_container;
  }

  resultat = enviar_i_rebre_dades(peticio);
  Serial.println(resultat);
  control = resultat.toInt();

  if (control == -1) {
    Serial.println("FALLO EN EL INSERT LAT LONG");
  } else if (control == 1) {
    Serial.println("REGISTRO ACTUALIZADO LAT LONG");
  }

  // Variable booleana para comparar si el container esta activo
  peticio = "?comprovacio=1&id_container=" + id_container;
  resultat = enviar_i_rebre_dades(peticio);
  resultat.trim();
  Serial.println(resultat);

  if (resultat == "f") {
    // ACCESO DENEGADO
    Serial.println("VARIABLE FALSE");
    activo = false;
    digitalWrite(RGB_GREEN, HIGH);
    digitalWrite(RGB_BLUE, HIGH);
    digitalWrite(RGB_RED, LOW);
  } else if (resultat == "t") {
    Serial.println("VARIABLE TRUE");
    activo = true;
    digitalWrite(RGB_GREEN, LOW);
    digitalWrite(RGB_BLUE, HIGH);
    digitalWrite(RGB_RED, HIGH);
  }
}


void loop() {
  // Sensor ultrasónico 1
  digitalWrite(trigPin, LOW);
  delayMicroseconds(2);
  digitalWrite(trigPin, HIGH);
  delayMicroseconds(10);
  digitalWrite(trigPin, LOW);
  long duration = pulseIn(echoPin, HIGH);
  float distance = duration * 0.034 / 2;

  // Sensor ultrasónico 2
  digitalWrite(trigPin2, LOW);
  delayMicroseconds(2);
  digitalWrite(trigPin2, HIGH);
  delayMicroseconds(10);
  digitalWrite(trigPin2, LOW);
  long duration2 = pulseIn(echoPin2, HIGH);
  float distance2 = duration2 * 0.034 / 2;

  // Test por pantalla
  Serial.println(distance2);
  Serial.println(distance);

  if (distance >= 10.00 || distance2 >= 10.00) {
    if (activo != true) {
      // Construir consulta para actualizar el valor de false a true
      peticio = "?activo=1&id_container=" + id_container;
      resultat = enviar_i_rebre_dades(peticio);
      control = resultat.toInt();
      
      if (control == -1) {
        // ACCESO DENEGADO
        Serial.println("FALLO EL CAMBIO DE ESTADO");
      } else if (control == 1) {
        Serial.println("REGISTRO ACTUALIZADO");
        activo = true;
      }
    }

    Serial.println("PERMITIDO");
    Serial.println("Lectura i accés del UID");
    delay(2000);

    // Bucle para leer y validar la tarjeta
    bool tarjetaValida = false;
    while (!tarjetaValida) {  // Mientras no sea válida, sigue esperando
      if (mfrc522.PICC_IsNewCardPresent()) {
        // Seleccionem una targeta
        if (mfrc522.PICC_ReadCardSerial()) {
          // Enviamos el UID de la tarjeta
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

          // Enviar UID al servidor para validarlo
          resultat = enviar_i_rebre_dades(peticio);
          control = resultat.toInt();

          if (control == -1) {
            // Si no es válido, pedimos una nueva tarjeta
            Serial.println("TARJETA NO VÁLIDA");
            digitalWrite(RGB_GREEN, HIGH); 
            digitalWrite(RGB_BLUE, LOW); 
            digitalWrite(RGB_RED, HIGH);
            delay(1000);  // Pequeña espera para evitar lecturas rápidas repetidas
          } else if (control == 0) {
            // ERROR con la tarjeta
            Serial.println("ERROR");
            delay(1000);  // Esperamos antes de intentar otra vez
          } else if (control == 1) {
            // Si la tarjeta es válida, salimos del bucle
            Serial.println("ACCESO PERMITIDO");
            tarjetaValida = true;  // Salimos del bucle
            mfrc522.PICC_HaltA();  // Aseguramos que la tarjeta se detenga
          }
        }
      }
    }

    abrirTapa();
    delay(15000);
    Serial.println("Tapa abierta");
    tiempoInicio = millis();
    while (digitalRead(boton) == HIGH && (millis() - tiempoInicio) < tiempoLimite) {
      delay(50);  // Añadimos un pequeño retraso para evitar saturar el micro
    }

    Serial.println("Cerrando tapa");
    cerrarTapa();
    delay(15000);  // Esperamos un momento antes de repetir el proceso

    peticio += "&pes=";
    pes = balanza.get_units(20);
    peticio += String(pes, 2);
    peticio += "&id_container=" + id_container;
    resultat = enviar_i_rebre_dades(peticio);
    control = resultat.toInt();
    
    if (control == -1) {
      // REGISTRO DENEGADO
      Serial.println("REGISTRO DENEGADO");
    } else if (control == 1) {
      Serial.println("REGISTRO COMPLETADO");
    }

    Serial.print("Peso: ");
    Serial.print(balanza.get_units(20));
    Serial.println(" kg");
    delay(500);
  } else {
    Serial.println("DENEGADO222");
    digitalWrite(RGB_GREEN, HIGH); 
    digitalWrite(RGB_BLUE, HIGH); 
    digitalWrite(RGB_RED, LOW);
    double lat, lng;
    if (obtenerCoordenadasGPS(lat, lng)) {
        Serial.println("GPS OBTENIDO EN FASE DE CIERRE");
        Serial.print("Lat: "); Serial.println(lat, 6);
        Serial.print("Lng: "); Serial.println(lng, 6);
        peticio = "?id_container=" + id_container + "&lat=" + String(lat, 6) + "&lng=" + String(lng, 6);
        resultat = enviar_i_rebre_dades(peticio);
        Serial.println(resultat);
        control = resultat.toInt();
        if (control == -1) {
            // ACCESO DENEGADO
            Serial.println("FALLO AL ACTUALIZAR EN CONTAINER");
        } else if (control == 1) {
            Serial.println("ACTUALIZACION EXITOSO EN CONTAINER");
        }
        peticio = "?id_container=" + id_container + "&lat=" + String(lat, 6) + "&lng=" + String(lng, 6) + "&pes=" + pes;
        resultat = enviar_i_rebre_dades(peticio);
        Serial.println(resultat);
        control = resultat.toInt();
        if (control == -1) {
            // ACCESO DENEGADO
            Serial.println("FALLO AL INSERTAR EN VACIADOS");
        } else if (control == 1) {
            Serial.println("REGISTRO EXITOSO EN VACIADOS");
        }
    }
    
    if (activo != false) {
      // Construir consulta para actualizar el valor de false a true
      peticio = "?activo=0&id_container=" + id_container;
      resultat = enviar_i_rebre_dades(peticio);
      Serial.println(resultat);
      control = resultat.toInt();
      
      if (control == -1) {
        // ACCESO DENEGADO
        Serial.println("FALLO EL CAMBIO DE ESTADO");
      } else if (control == 1) {
        Serial.println("REGISTRO ACTUALIZADO");
        activo = false;
      }
      
    }

    delay(5000);
  }
}

String enviar_i_rebre_dades(String peticio){
  Serial.println("Enviando UID a ESP32: " + peticio);
  Serial2.println(peticio);
  delay(2000);
  resultat = "0";
  while (resultat == "0") {
    resultat = Serial2.readStringUntil('\n');
  }
  return resultat;
}
void abrirTapa() {
  digitalWrite(PinIN1, HIGH);
  digitalWrite(PinIN2, LOW);
}

void cerrarTapa() {
  digitalWrite(PinIN1, LOW);
  digitalWrite(PinIN2, HIGH);
}
bool obtenerCoordenadasGPS(double &lat, double &lng) {
  unsigned long gpsTiempoInicio = millis();
  while (millis() - gpsTiempoInicio < 5000) {
    while (gpsSerial.available()) {
      char c = gpsSerial.read();
      gps.encode(c);
      if (gps.location.isUpdated()) {
        lat = gps.location.lat();
        lng = gps.location.lng();
        return true;
      }
    }
  }
  return false;
}