// Prueba GPS
#include <TinyGPS++.h>

TinyGPSPlus gps;

void setup() {
  Serial.begin(9600); // Para el monitoreo por el puerto USB
  Serial1.begin(9600); // Para la comunicación con el GPS (usando Serial1 en el Mega)
  Serial.println("Inicializando...");
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
}
