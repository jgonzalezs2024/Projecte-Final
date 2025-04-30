#include <SoftwareSerial.h>
#include <SPI.h>
#include <MFRC522.h>
#include <TinyGPS++.h>
#define RST_PIN 5 // Pin número 5 para el RST del RC522
#define SS_PIN 53 // Pin número 53 para el SS (SDA) del RC522
TinyGPSPlus gps;
SoftwareSerial Arduino_Serial(11, 12);
LiquidCrystal lcd(43, 41, 39, 37, 35, 33);

MFRC522 mfrc522(SS_PIN, RST_PIN); // Creem l’objete per a el RC522
byte ActualUID[4]; // Emmagatzemarà codi únic llegit de la targeta
String peticio, str, cC, resultat;
int c;

void setup() {
  Arduino_Serial.begin(115200);
  Serial.begin(115200); // Iniciem la comunicació en sèrie
  Serial1.begin(9600);
  SPI.begin(); // Iniciem el Bus SPI
  mfrc522.PCD_Init(); // Iniciem el MFRC522
  Serial.println("Lectura i accés del UID");
  lcd.begin(16, 2);
}

void loop() {
  while (Serial1.available()) {
    char c = Serial1.read();
    Serial.print(c); // Mostrar datos crudos del GPS (NMEA)
    gps.encode(c);

    if (gps.location.isUpdated()) {
      Serial.print("Satélites: ");
      Serial.println(gps.satellites.value());

      Serial.print("Latitud: ");
      Serial.println(gps.location.lat(), 6);

      Serial.print("Longitud: ");
      Serial.println(gps.location.lng(), 6);
    }
  }
    // Comprovem si hi ha noves targetes presents
    if (mfrc522.PICC_IsNewCardPresent()) {
        // Seleccionem una targeta
        if (mfrc522.PICC_ReadCardSerial()) {
            // Enviem serialement el seu UID
            String uidString = "q=";
            for (byte i = 0; i < mfrc522.uid.size; i++) {
                if (mfrc522.uid.uidByte[i] < 0x10) {
                    uidString += " 0";
                } else {
                    uidString += "";
                }
                uidString += String(mfrc522.uid.uidByte[i], HEX);
            }

            peticio = uidString;
            Serial.println("Enviando UID a ESP32: " + peticio);
            // URL que vull enviar al servidor
            // Final de la petició
            peticio.concat("\n");
            enviar_dades_arduino(peticio);
            resultat = "0";
            // Mentre el resultat de la resposta sigui la cadena "0"
            // resto a l'espera de la resposta
            while (resultat == "0") {
                resultat = llegir_dades_arduino();
            }
            // Si arribo aquí és perquè ja tinc la resposta
            c = 0;
            str = "";
            // Comparem el UID per a determinar si és un dels nostres usuaris
            // Comparar resultat amb les possibles respostes
            if (resultat == "ACCESO DENEGADO") {
                // ACCESO DENEGADO (en mayúsculas)
                lcd.setCursor(1, 1);
                lcd.print("Acceso denegado");
            } else if (resultat == "ERROR") {
                // ERROR en la respuesta
                lcd.setCursor(1, 1);
                lcd.print("Error en la solicitud");
            } else {
                // Mostrar el nombre de usuario si se ha recibido un nombre Mostrar el nombre y apellido
                lcd.setCursor(1, 1);
                lcd.print("Usuario: ");
                lcd.print(resultat);
            }

      // Acabem la lectura de la targeta actual
      mfrc522.PICC_HaltA();
    }
  }
}

// Funció per a comparar dos vectors de dades
boolean compareArray(byte array1[], byte array2[]) {
  if (array1[0] != array2[0]) return false;
  if (array1[1] != array2[1]) return false;
  if (array1[2] != array2[2]) return false;
  if (array1[3] != array2[3]) return false;
  return true;
}
void enviar_dades_arduino(String dades) {
    // Serial.println(dades);
    Arduino_Serial.print(dades);
}

// Funció per a esperar la resposta de la ESP
String llegir_dades_arduino() {
    // Llegim les dades caràcter a caràcter
    while (Arduino_Serial.available() > 0) {
        // Llegim un caràcter i, si no és \n,
        // l’afegim a la cadena
        cC = Arduino_Serial.read();
        if (cC == '\n') {
            break;
        } else {
            str += cC;
        }
    }

    if (cC == '\n') {
        // Hem obtingut una resposta perquè hem arribat al caràcter
        // de fí de resposta que es el salt de línia (\n)
        // Retornem la petició rebuda
        return str;
    }

    // Si no estem rebent dades de la ESP retornem la cadena "0"
    // fet que indica que no estem rebent resposta
    return "0";
}
