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
SoftwareSerial Arduino_Serial(11, 12);
MFRC522 mfrc522(SS_PIN, RST_PIN); // Creem l’objete per a el RC522
byte ActualUID[4]; // Emmagatzemarà codi únic llegit de la targeta
String peticio, str, cC, resultat, prova, consulta;
int c;
bool prova1;
const int trigPin = 9;
const int echoPin = 10;

void setup() {
  pinMode(trigPin, OUTPUT);     // Define TRIG como saída
  pinMode(echoPin, INPUT);      // Define ECHO como entrada
  Arduino_Serial.begin(115200);
  Serial.begin(115200); // Iniciem la comunicació en sèrie
  Serial1.begin(9600);
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
            String uidString = "rfid=";
            for (byte i = 0; i < mfrc522.uid.size; i++) {
                if (mfrc522.uid.uidByte[i] < 0x10) {
                    uidString += " 0";
                } else {
                    uidString += "";
                }
                uidString += String(mfrc522.uid.uidByte[i], HEX);
            }
            consulta=uidString;
            prova1 = false;
            mfrc522.PICC_HaltA();
        }
      }
    }
    Serial.println("Enviando UID a ESP32: " + peticio);
    peticio.concat("\n");
    enviar_dades_arduino(peticio);
    resultat = "0";
    while (resultat == "0") {
      resultat = llegir_dades_arduino();
    }
    c = 0;
    str = "";
    if (resultat == "-1") {
      // ACCESO DENEGADO (en mayúsculas)
      lcd.setCursor(1, 1);
      lcd.print("Acceso denegado");
    } else if (resultat == "0") {
      Serial.println("ERROR");
    } else {
      // ABRIR PUERTA
      // DELAY(30000)
      // CERRAR PUERTA
      Serial.print("Peso: ");
      Serial.print(balanza.get_units(20),3);
      Serial.println(" kg");
      delay(500);



    }
  } else {
    Serial.println("DENEGADO");
  }




  // put your main code here, to run repeatedly:

}
