/* 
*  TABLA USUARIO: 
*  Guardar los usuario de la web
*  El nick es unico, el nombre real no es necesario al registrarse
*/
CREATE TABLE final_usuario(
	idUser INT(10) UNSIGNED AUTO_INCREMENT,
	nick VARCHAR(30) NOT NULL,
	name VARCHAR(40),
	password VARCHAR(50) NOT NULL,	
	email VARCHAR(30) NOT NULL,
	CONSTRAINT PK_usuario PRIMARY KEY (idUser),
	CONSTRAINT U_usuario UNIQUE (nick)	
);

/* 
*  TABLA CATEGORIA: 
*  Guardar las categorias a las que pueden pertenecer los anuncios
*  La categoria otros es inborrable y siempre esta, asi que la metemos ya.
*/
CREATE TABLE final_categoria(
    idCategoria INT(5) UNSIGNED AUTO_INCREMENT,
	categoria VARCHAR(30) NOT NULL,
	CONSTRAINT PK_categoria PRIMARY KEY (idCategoria)
);

INSERT INTO final_categoria VALUES(0, 'Otros');

/* 
*  TABLA ANUNCIO: 
*  El anuncio pertenece a un usuario y una categoria
*  El anuncio tiene un precio una fecha.
*  Si se borra el dueño del anuncio se borra el anuncio. No tiene sentido mantenerlo.
*  Estado: 1 (activo), 0(borrado/cancelado), 2(vendido). Solo los anuncios estado 1 se muestran al usuario normal
*  ¡¡¡ InnoDB no permite ON DELETE SET DEFAULT por lo que cuando queramos borrar
*  una categoria en PHP primero hay que manualmente con php cambiar la categoria de todos
*  esos anuncios a 0!!!
*/
CREATE TABLE final_anuncio(
	idAnuncio INT(10) UNSIGNED AUTO_INCREMENT,
	idUser INT(10) UNSIGNED NOT NULL,
	idCategoria INT(5) UNSIGNED DEFAULT 0,
    fecha DATETIME DEFAULT CURRENT_TIMESTAMP,
    precio DECIMAL(7,2) DEFAULT 0,
    titulo VARCHAR(50) NOT NULL,
    descripcion VARCHAR(2000),
    localizacion VARCHAR(50) NOT NULL,
    telefono VARCHAR(15), 
    estado INT(1) DEFAULT 1,
    idComprador INT(10) UNSIGNED,
	CONSTRAINT PK_anuncio PRIMARY KEY (idAnuncio),
	CONSTRAINT FK_anuncio_categoria FOREIGN KEY (idCategoria) references final_categoria(idCategoria),
	CONSTRAINT FK_anuncio_usuario FOREIGN KEY (idUser) references final_usuario (idUser) ON DELETE CASCADE,
    CONSTRAINT FK_anuncio_usuario2 FOREIGN KEY (idComprador) references final_usuario (idUser) ON DELETE SET NULL
);

/* 
*  TABLA COMENTARIO: 
*  El comentario pertenece a un anuncio y un usuario
*  El comentario tiene un maximo de 400 caracteres
*  El comentario puede hacer referencia a otro comentario
*/
CREATE TABLE final_comentario(
    idComentario INT(10) UNSIGNED AUTO_INCREMENT,
	idUser INT(10) UNSIGNED NOT NULL,
	idAnuncio INT(10) UNSIGNED NOT NULL,
	comentario VARCHAR(400) NOT NULL,
    idPadre INT(10) UNSIGNED,
    fecha DATETIME DEFAULT CURRENT_TIMESTAMP,
	CONSTRAINT PK_comentario PRIMARY KEY (idComentario),
	CONSTRAINT FK_comentario_usuario FOREIGN KEY (idUser) references final_usuario(idUser) ON DELETE CASCADE,
	CONSTRAINT FK_comentario_anuncio FOREIGN KEY (idAnuncio) references final_anuncio(idAnuncio) ON DELETE CASCADE,
    CONSTRAINT FK_comentario_comentario FOREIGN KEY (idPadre) references final_comentario(idComentario) ON DELETE CASCADE
);

/* 
*  TABLA FAVORITO: 
*  El favorito se compone de un usuario y un anuncio
*/
CREATE TABLE final_favorito(
	idUser INT(10) UNSIGNED NOT NULL,
	idAnuncio INT(10) UNSIGNED NOT NULL,
	CONSTRAINT PK_favorito PRIMARY KEY (idUser,idAnuncio),
	CONSTRAINT FK_favorito_usuario FOREIGN KEY (idUser) references final_usuario(idUser) ON DELETE CASCADE,
	CONSTRAINT FK_favorito_anuncio FOREIGN KEY (idAnuncio) references final_anuncio(idAnuncio) ON DELETE CASCADE
);

/* 
*  TABLA IMAGEN: 
*  La imagen pertenece a un usuario, y tiene un id 
*  (En un anuncio puede haber mas de una imagen)
*  En los atributos se guarda el path a la imagen
*/
CREATE TABLE final_imagen(
	idAnuncio INT(10) UNSIGNED NOT NULL,
	idImagen INT(4) NOT NULL,
	big VARCHAR(100) NOT NULL,
	medium VARCHAR(100) NOT NULL,
	small VARCHAR(100) NOT NULL,
	CONSTRAINT PK_imagen PRIMARY KEY (idAnuncio, idImagen),
	CONSTRAINT FK_imagen_anuncio FOREIGN KEY (idAnuncio) references final_anuncio (idAnuncio) ON DELETE CASCADE
);
