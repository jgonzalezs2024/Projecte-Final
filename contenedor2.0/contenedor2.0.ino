#include <TinyGPS++.h>
#include <SoftwareSerial.h>
#include <SPI.h>
#include <MFRC522.h>
#include "HX711.h"
#define RST_PIN 5 // Pin número 5 para el RST del RC522
#define SS_PIN 53 // Pin número 53 para el SS (SDA) del RC522

const int DOUT=A1;
const int CLK=A0;
HX711 balanza;

TinyGPSPlus gps;
MFRC522 mfrc522(SS_PIN, RST_PIN); // Creem l’objete per a el RC522
byte ActualUID[4]; // Emmagatzemarà codi únic llegit de la targeta
String peticio, resultat, consulta;
bool prova1;
const int trigPin = 9;
const int echoPin = 10;
void setup() {
  pinMode(trigPin, OUTPUT);     // Define TRIG como saída
  pinMode(echoPin, INPUT);
  Serial.begin(115200);
  Serial1.begin(57600);
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
  balanza.set_scale(12200); // Establecemos la escala
  balanza.tare(20);  //El peso actual es considerado Tara.
  Serial.println("Listo para pesar");

}

void loop() {
  while (Serial1.available()) {
  char c = Serial1.read();
  gps.encode(c);

  if (gps.location.isUpdated()) {
    double lat = gps.location.lat();
    double lng = gps.location.lng();         
    Serial.print("Latitud: ");
    Serial.println(gps.location.lat(), 6);

    Serial.print("Longitud: ");
    Serial.println(gps.location.lng(), 6);
  }
}
  digitalWrite(trigPin, LOW);
  delayMicroseconds(2);
  digitalWrite(trigPin, HIGH);
  delayMicroseconds(10);
  digitalWrite(trigPin, LOW);

  long duration = pulseIn(echoPin, HIGH);

  float distance = duration * 0.034 / 2;


  if (distance >= 15.00){
    Serial.println("PERMITIDO");
    Serial.println("Lectura i accés del UID");
    delay(2000);
    prova1 = true;
    while (prova1){
      if (mfrc522.PICC_IsNewCardPresent()) {
        // Seleccionem una targeta
        if (mfrc522.PICC_ReadCardSerial()) {
            // Enviem serialement el seu UID
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
            prova1 = false;
            mfrc522.PICC_HaltA();
        }
      }
    }
    resultat = enviar_i_rebre_dades(peticio);
    Serial.println(resultat);
    int miNumero = resultat.toInt();
    if (miNumero == -1) {
      // ACCESO DENEGADO (en mayúsculas)
      Serial.println("DENEGADO");
    } else if (miNumero == 0) {
      Serial.println("ERROR");
    } else if (miNumero == 1){
      // ABRIR PUERTA
      // DELAY(30000)
      // CERRAR PUERTA
      Serial.println("ACCESO PERMITIDO");
      Serial.print("Peso: ");
      Serial.print(balanza.get_units(20),3);
      Serial.println(" kg");
      delay(500);



    }
  } else {
    Serial.println("DENEGADO222");
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
