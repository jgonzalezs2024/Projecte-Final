const int PinIN1 = 35;
const int PinIN2 = 37;

void setup() {
  // inicializar la comunicación serial a 9600 bits por segundo:
  Serial.begin(115200);
  // configuramos los pines como salida
  pinMode(PinIN1, OUTPUT);
  pinMode(PinIN2, OUTPUT);
  MotorAntihorario();
  delay(20000);
}

void loop() {
  
  MotorHorario();
  Serial.println("Giro del Motor en sentido horario");
  delay(20000);
  
  MotorAntihorario();
  Serial.println("Giro del Motor en sentido antihorario");
  delay(20000);
  
  MotorStop();
  Serial.println("Motor Detenido");
  delay(20000);
  
}

//función para girar el motor en sentido horario
void MotorHorario()
{
  digitalWrite (PinIN1, HIGH);
  digitalWrite (PinIN2, LOW);
}
//función para girar el motor en sentido antihorario
void MotorAntihorario()
{
  digitalWrite (PinIN1, LOW);
  digitalWrite (PinIN2, HIGH);
}

//función para apagar el motor
void MotorStop()
{
  digitalWrite (PinIN1, LOW);
  digitalWrite (PinIN2, LOW);
}
