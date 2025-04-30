CREATE TABLE containers (
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
    id_container INTEGER PRIMARY KEY REFERENCES containers(id),
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
