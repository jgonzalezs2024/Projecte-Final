const int trigPin = 9;
const int echoPin = 10;
const int trigPin2 = 11;
const int echoPin2 = 12;

void setup() {
  Serial.begin(115200);

  pinMode(trigPin, OUTPUT);
  pinMode(echoPin, INPUT);
  pinMode(trigPin2, OUTPUT);
  pinMode(echoPin2, INPUT);

  Serial.println("Test de sensores ultrasónicos iniciado.");
}

void loop() {
  // Sensor 1
  digitalWrite(trigPin, LOW);
  delayMicroseconds(2);
  digitalWrite(trigPin, HIGH);
  delayMicroseconds(10);
  digitalWrite(trigPin, LOW);
  long duration1 = pulseIn(echoPin, HIGH);
  float distance1 = duration1 * 0.034 / 2;

  // Sensor 2
  digitalWrite(trigPin2, LOW);
  delayMicroseconds(2);
  digitalWrite(trigPin2, HIGH);
  delayMicroseconds(10);
  digitalWrite(trigPin2, LOW);
  long duration2 = pulseIn(echoPin2, HIGH);
  float distance2 = duration2 * 0.034 / 2;

  // Mostrar la salida en pantalla
  Serial.print("Distancia Sensor 1: ");
  Serial.print(distance1);
  Serial.println(" cm");

  Serial.print("Distancia Sensor 2: ");
  Serial.print(distance2);
  Serial.println(" cm");

  Serial.println("---------------------");
  delay(1000);
}

