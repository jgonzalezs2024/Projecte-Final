#define RGB_GREEN 3
#define RGB_BLUE 2
#define RGB_RED 6
#define espera 300

void setup() {
  // put your setup code here, to run once:
pinMode (RGB_RED, OUTPUT);
pinMode (RGB_GREEN, OUTPUT);
pinMode (RGB_BLUE, OUTPUT);
}


void loop() {
  // put your main code here, to run repeatedly:
digitalWrite(RGB_GREEN, LOW); 
digitalWrite(RGB_BLUE, HIGH); 
digitalWrite(RGB_RED, HIGH); 
delay (espera); 
digitalWrite(RGB_GREEN, LOW); 
digitalWrite(RGB_BLUE, LOW); 
digitalWrite(RGB_RED, HIGH); 
delay (espera); 
digitalWrite(RGB_GREEN, HIGH); 
digitalWrite(RGB_BLUE, HIGH); 
digitalWrite(RGB_RED, LOW); 
delay(espera); 
}