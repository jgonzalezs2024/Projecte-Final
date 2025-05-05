// ESP32 code
#define RXD2 32
#define TXD2 33

void setup() {
  Serial.begin(115200);       // Debug
  Serial2.begin(9600, SERIAL_8N1, RXD2, TXD2); // Serial2
  Serial.println("ESP32 listo.");
}

void loop() {
  if (Serial2.available()) {
    String msg = Serial2.readStringUntil('\n');
    Serial.print("Recibido del Mega: ");
    Serial.println(msg);
  }

  // Enviar mensaje al Mega
  Serial2.println("Hola desde ESP32");
  delay(2000);
}
