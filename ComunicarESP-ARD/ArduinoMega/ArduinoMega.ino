#include <SoftwareSerial.h>

SoftwareSerial espSerial(11, 12); // RX, TX

void setup() {
  Serial.begin(115200);
  espSerial.begin(9600);
  Serial.println("Mega listo.");
}

void loop() {
  if (espSerial.available()) {
    String msg = espSerial.readStringUntil('\n');
    Serial.print("Recibido del ESP32: ");
    Serial.println(msg);
  }

  espSerial.println("Hola desde Mega");
  delay(2000);
}
