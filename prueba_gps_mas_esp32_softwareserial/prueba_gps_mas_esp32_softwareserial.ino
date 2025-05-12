#include <TinyGPS++.h>
#include <SoftwareSerial.h>

// Pines para GPS con SoftwareSerial
#define GPS_RX 10  // Conectar al TX del GPS
#define GPS_TX 11  // (opcional, normalmente no se usa)

// Crear el puerto GPS
SoftwareSerial gpsSerial(GPS_RX, GPS_TX);
TinyGPSPlus gps;

void setup() {
  Serial.begin(115200);      // Monitor serial en la PC
  gpsSerial.begin(9600);     // GPS por SoftwareSerial
  Serial2.begin(9600);       // ESP32 conectado a Serial2 (Mega: RX2 = 17, TX2 = 16)

  Serial.println("Iniciando prueba GPS (SoftwareSerial) + ESP32 (Serial2)...");
}

void loop() {
  // Leer datos del GPS
  while (gpsSerial.available()) {
    char c = gpsSerial.read();         // Mostrar datos crudos del GPS
    gps.encode(c);
  }

  // Si hay nueva ubicación
  if (gps.location.isUpdated()) {
    double lat = gps.location.lat();
    double lng = gps.location.lng();

    Serial.println("----- Ubicación actualizada -----");
    Serial.print("Latitud: ");
    Serial.println(lat, 6);
    Serial.print("Longitud: ");
    Serial.println(lng, 6);

    // Enviar a ESP32
    String mensaje = "?lat=" + String(lat, 6) + "&lng=" + String(lng, 6);
    Serial2.println(mensaje);
    Serial.print("Enviado a ESP32: ");
    Serial.println(mensaje);
  }

  // Leer respuesta del ESP32
  if (Serial2.available()) {
    String respuesta = Serial2.readStringUntil('\n');
    Serial.print("Respuesta desde ESP32: ");
    Serial.println(respuesta);
  }

  delay(100);
}
