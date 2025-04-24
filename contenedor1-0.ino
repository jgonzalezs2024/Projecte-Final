#include <TinyGPS++.h>
#include <SoftwareSerial.h>
#include <SPI.h>
#include <MFRC522.h>

#define RST_PIN 5 // Pin número 5 para el RST del RC522
#define SS_PIN 53 // Pin número 53 para el SS (SDA) del RC522
bool vacio;
TinyGPSPlus gps;
SoftwareSerial Arduino_Serial(11, 12);
MFRC522 mfrc522(SS_PIN, RST_PIN); // Creem l’objete per a el RC522
byte ActualUID[4]; // Emmagatzemarà codi únic llegit de la targeta
String peticio, str, cC, resultat, prova;
int c;
bool prova1 = true;

void setup() {
  
  pinMode(PIR1_PIN, INPUT);
  vacio = true;
  Arduino_Serial.begin(115200);
  Serial.begin(115200); // Iniciem la comunicació en sèrie
  Serial1.begin(9600);
  SPI.begin(); // Iniciem el Bus SPI
  mfrc522.PCD_Init(); // Iniciem el MFRC522
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
  






  peticio = "";
  }
  if (vacio == true)
    Serial.println("Lectura i accés del UID");
    delay(5000);

  // put your main code here, to run repeatedly:

}
