// DEFINICION DE LIBRERIAS //

#include <TinyGPS++.h>
#include <SPI.h>
#include <MFRC522.h>
#include "HX711.h"


#define RST_PIN 5
#define SS_PIN 53
#define RGB_GREEN 3
#define RGB_BLUE 2
#define RGB_RED 7
const int DOUT=A1;
const int CLK=A0;
const int PinIN1 = 35;
const int PinIN2 = 37;
const int boton = 27;
const int trigPin = 9;
const int echoPin = 10;
const int trigPin2 = 11;
const int echoPin2 = 12;
bool uidValido, activo, tieneCoordenadas, puertaAbierta;
float pes, distance, distance2;
int control;
long duration, duration2;
double lat, lng;
char c;
HX711 balanza;
MFRC522 mfrc522(SS_PIN, RST_PIN);
String id_container = "1";
TinyGPSPlus gps;
byte ActualUID[4];
String peticio, resultat;

void setup() {
  pinMode(boton, INPUT_PULLUP);
  pinMode(PinIN1, OUTPUT);
  pinMode(PinIN2, OUTPUT);
  pinMode (RGB_RED, OUTPUT);
  pinMode (RGB_GREEN, OUTPUT);
  pinMode (RGB_BLUE, OUTPUT);
  pinMode(trigPin, OUTPUT);     // Define TRIG como saída
  pinMode(echoPin, INPUT);
  pinMode(trigPin2, OUTPUT);
  pinMode(echoPin2, INPUT);
  Serial.begin(115200);
  Serial1.begin(9600);
  Serial2.begin(9600); // TX2 = 16, RX2 = 17
  delay(1000);
  Serial.println("Arduino Mega listo.");
  SPI.begin(); // Iniciem el Bus SPI
  mfrc522.PCD_Init(); // Iniciem el MFRC522
  balanza.begin(DOUT, CLK);
  Serial.print("Lectura del valor del ADC:  ");
  Serial.println(balanza.read());
  Serial.println("No ponga ningun  objeto sobre la balanza");
  Serial.println("Destarando...");
  Serial.println("...");
  balanza.set_scale(-40000); // Establecemos la escala
  balanza.tare(20);  //El peso actual es considerado Tara.
  Serial.println("Listo para pesar");

  tieneCoordenadas = false;
  while (Serial1.available()) {
    c = Serial1.read();
    gps.encode(c);

    if (gps.location.isUpdated()) {
      tieneCoordenadas = true;

      lat = gps.location.lat();
      lng = gps.location.lng();         

      Serial.print("Latitud: ");
      Serial.println(lat, 6);
      Serial.print("Longitud: ");
      Serial.println(lng, 6);

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
  peticio="?comprovacio=1&id_container=" + id_container;
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

  } else if (resultat == "t"){
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
  duration = pulseIn(echoPin, HIGH);
  distance = duration * 0.034 / 2;

  // Sensor ultrasónico 2
  digitalWrite(trigPin2, LOW);
  delayMicroseconds(2);
  digitalWrite(trigPin2, HIGH);
  delayMicroseconds(10);
  digitalWrite(trigPin2, LOW);
  duration2 = pulseIn(echoPin2, HIGH);
  distance2 = duration2 * 0.034 / 2;

  if (distance >= 10.00 || distance2 >= 10.00){
    digitalWrite(RGB_GREEN, LOW); 
    digitalWrite(RGB_BLUE, HIGH); 
    digitalWrite(RGB_RED, HIGH);
    delay(2000);
    if (activo != true) {
      // Construir consulta para actualizar el valor de false a true
      peticio="?activo=1&id_container=" + id_container;
      resultat = enviar_i_rebre_dades(peticio);
      control = resultat.toInt();
      if (control == -1) {
        // ACCESO DENEGADO
        Serial.println("FALLO EL CAMBIO DE ESTADO");
      } else if (control == 1){
        Serial.println("REGISTRO ACTUALIZADO");
        activo = true;
      }
    }
    Serial.println("PERMITIDO");
    Serial.println("Lectura i accés del UID");
    delay(2000);

    uidValido = false;
    while (uidValido == false){
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
            peticio=uidString;
            uidValido = true;
            mfrc522.PICC_HaltA();
        }
      }
    }
    resultat = enviar_i_rebre_dades(peticio);
    Serial.println(resultat);
    control = resultat.toInt();
    if (control == -1) {
      // ACCESO DENEGADO (en mayúsculas)
      Serial.println("DENEGADO");
      digitalWrite(RGB_GREEN, HIGH); 
      digitalWrite(RGB_BLUE, LOW); 
      digitalWrite(RGB_RED, HIGH);
      delay(5000);
    } else if (control == 0) {
      Serial.println("ERROR");
    } else if (control == 1){
      // ABRIR PUERTA
      // DELAY(30000)
      // CERRAR PUERTA
      levantarTapa();
      delay(20000);
      Serial.println("PUERTA ABIERTA");
      puertaAbierta = true;

      while (puertaAbierta) {
        if (digitalRead(boton) == LOW) {
          Serial.println("Condición para cerrar puerta alcanzada");

          cerrarTapa();
          delay(20000);
          Serial.println("PUERTA CERRADA");

          puertaAbierta = false;
        }

        delay(50);  // Evita sobrecargar el micro
      }
      
      pararMotor();
      Serial.println("Motor Detenido");
      delay(1000);
      peticio += "&pes=";
      pes = balanza.get_units(20);
      peticio += String(pes, 2);
      peticio += "&id_container=" + id_container;
      enviar_i_rebre_dades(peticio);
      Serial.println(resultat);
      control = resultat.toInt();
      if (control == -1) {
        // ACCESO DENEGADO
        Serial.println("REGISTRO DENEGADO");
      } else if (control == 1){
        Serial.println("REGISTRO COMPLETADO");
      }
      Serial.println("ACCESO PERMITIDO");
      Serial.print("Peso: ");
      Serial.print(balanza.get_units(20));
      Serial.println(" kg");
      delay(500);
    }
  } else {
    digitalWrite(RGB_GREEN, HIGH); 
    digitalWrite(RGB_BLUE, HIGH); 
    digitalWrite(RGB_RED, LOW);
    Serial.println("DENEGADO222");
    if (tieneCoordenadas != true) {
      while (Serial1.available()) {
        c = Serial1.read();
        gps.encode(c);

        if (gps.location.isUpdated()) {
          tieneCoordenadas = true;

          lat = gps.location.lat();
          lng = gps.location.lng();         

          Serial.print("Latitud: ");
          Serial.println(lat, 6);
          Serial.print("Longitud: ");
          Serial.println(lng, 6);

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
    }
    delay(2000);
    if (activo != false) {
      // Construir consulta para actualizar el valor de false a true
      peticio="?activo=0&id_container=" + id_container;
      resultat = enviar_i_rebre_dades(peticio);
      Serial.println(resultat);
      control = resultat.toInt();
      if (control == -1) {
        // ACCESO DENEGADO
        Serial.println("FALLO EL CAMBIO DE ESTADO");
      } else if (control == 1){
        Serial.println("REGISTRO ACTUALIZADO");
        activo = false;

        peticio="?activo=0&id_container=" + id_container;

      }
      
      peticio="?id_container=" + id_container + "&lat=45.224152&lng=3.725570" + "&pes=" + String(pes, 2);
      resultat = enviar_i_rebre_dades(peticio);
      Serial.println(resultat);
      control = resultat.toInt();
      if (control == -1) {
        // ACCESO DENEGADO
        Serial.println("FALLO AL INSERTAR EN VACIADOS");
      } else if (control == 1){
        Serial.println("REGISTRO EXITOSO EN VACIADOS");
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
//función para levantar la tapa
void levantarTapa()
{
  digitalWrite (PinIN1, HIGH);
  digitalWrite (PinIN2, LOW);
}
//función para bajar la tapa
void cerrarTapa()
{
  digitalWrite (PinIN1, LOW);
  digitalWrite (PinIN2, HIGH);
}

//función para apagar el motor
void pararMotor()
{
  digitalWrite (PinIN1, LOW);
  digitalWrite (PinIN2, LOW);
}
