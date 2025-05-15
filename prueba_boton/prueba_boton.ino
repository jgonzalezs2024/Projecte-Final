const int boton = 3;

void setup() {
  pinMode(boton, INPUT_PULLUP);
  Serial.begin(115200);
}

void loop() {
  // ... código previo

  Serial.println("PUERTA ABIERTA");

  bool puertaAbierta = true;

  while (puertaAbierta) {
    if (digitalRead(boton) == LOW) {
      Serial.println("Botón presionado: cerrar puerta");
      puertaAbierta = false;
    }

    delay(50);  // evita saturar el micro
  }

  // Aquí cierras la puerta
  Serial.println("PUERTA CERRADA");
  // Código para cerrar la puerta (relé, motor, etc.)
}
