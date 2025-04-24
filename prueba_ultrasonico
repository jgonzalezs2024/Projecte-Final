// Definições dos pinos
const int trigPin = 9;
const int echoPin = 10;

void setup() {
  Serial.begin(115200);           // Inicializa a comunicação serial
  pinMode(trigPin, OUTPUT);     // Define TRIG como saída
  pinMode(echoPin, INPUT);      // Define ECHO como entrada
}

void loop() {
  // Gera o pulso de trigger
  digitalWrite(trigPin, LOW);
  delayMicroseconds(2);
  digitalWrite(trigPin, HIGH);
  delayMicroseconds(10);
  digitalWrite(trigPin, LOW);

  // Lê o tempo de resposta do pulso
  long duration = pulseIn(echoPin, HIGH);
  Serial.println(duration);

  // Calcula a distância em centímetros
  float distance = duration * 0.034 / 2;

  // Exibe no monitor serial
  Serial.print("Distancia: ");
  Serial.print(distance);
  Serial.println(" cm");

  delay(5000); // Aguarda meio segundo antes da próxima medição
}
