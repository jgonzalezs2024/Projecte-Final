CREATE TABLE container (
    id SERIAL PRIMARY KEY,
    tipo VARCHAR(50) NOT NULL,
    activo BOOLEAN NOT NULL,
    longitud_actual DOUBLE PRECISION NOT NULL,
    latitud_actual DOUBLE PRECISION NOT NULL
);

CREATE TABLE rfid (
    num_serie VARCHAR(20) PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    apellido VARCHAR(100) NOT NULL,
    fecha_nacimiento DATE NOT NULL
);

CREATE TABLE metricas (
    id_container INTEGER PRIMARY KEY REFERENCES container(id),
    peso_actual NUMERIC(10,2) NOT NULL,
    fecha_actual TIMESTAMP NOT NULL,
    num_serie VARCHAR(255) REFERENCES rfid(num_serie)
);

CREATE TABLE vaciados (
    id_container INTEGER REFERENCES container(id),
    fecha_vaciado TIMESTAMP,
    latitud_vaciado DOUBLE PRECISION NOT NULL,
    longitud_vaciado DOUBLE PRECISION NOT NULL,
    peso_vaciado NUMERIC(10,2) NOT NULL,
    PRIMARY KEY (id_container, fecha_vaciado)
);


INSERT INTO rfid (num_serie, nombre, apellido, fecha_nacimiento)
VALUES ('23cea514', 'Lucía', 'González', '1992-05-14');

INSERT INTO rfid (num_serie, nombre, apellido, fecha_nacimiento)
VALUES ('c3f1a828', 'Carlos', 'Ramírez', '1987-11-23');

INSERT INTO rfid (num_serie, nombre, apellido, fecha_nacimiento)
VALUES ('f394d8a5', 'Ana', 'Martínez', '2000-01-10');

INSERT INTO rfid (num_serie, nombre, apellido, fecha_nacimiento)
VALUES ('53b5a11a', 'Jorge', 'Pérez', '1995-07-03');

INSERT INTO rfid (num_serie, nombre, apellido, fecha_nacimiento)
VALUES ('43434da9', 'María', 'Lozano', '1982-09-27');