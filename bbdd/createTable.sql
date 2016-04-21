CREATE TABLE final_usuario(
	idUser INT(10) UNSIGNED AUTO_INCREMENT,
	user VARCHAR(30) NOT NULL,
	name VARCHAR(30) NOT NULL,
	password VARCHAR(50) NOT NULL,	
	email VARCHAR(30) NOT NULL,	
	qSec VARCHAR(30) NOT NULL,
	aSec VARCHAR(30) NOT NULL,
	CONSTRAINT PK_usuario PRIMARY KEY (idUser),
	CONSTRAINT U_usuario UNIQUE (user)	
);

CREATE TABLE final_anuncio(
	idAnuncio INT(10) UNSIGNED AUTO_INCREMENT,
	idUser INT(10) UNSIGNED NOT NULL,	
	CONSTRAINT PK_anuncio PRIMARY KEY (idAnuncio),
	CONSTRAINT FK_anuncio_usuario FOREIGN KEY (idUser) references final_usuario (idUser)
);

CREATE TABLE final_producto(
	idProducto INT(10) UNSIGNED AUTO_INCREMENT,
	idUser INT(10) UNSIGNED NOT NULL,
	idAnuncio INT(10) UNSIGNED NOT NULL,
	CONSTRAINT PK_producto PRIMARY KEY (idProducto),
	CONSTRAINT FK_producto_usuario FOREIGN KEY (idUser) references final_usuario (idUser),
	CONSTRAINT FK_producto_anuncio FOREIGN KEY (idAnuncio) references final_anuncio (idAnuncio)
);

CREATE TABLE final_comentario(
	idUser INT(10) UNSIGNED NOT NULL,
	idAnuncio INT(10) UNSIGNED NOT NULL,
	comentario VARCHAR(200) NOT NULL,
	CONSTRAINT PK_comentario PRIMARY KEY (idUser,idAnuncio),
	CONSTRAINT FK_comentario_usuario FOREIGN KEY (idUser) references final_usuario(idUser),
	CONSTRAINT FK_comentario_anuncio FOREIGN KEY (idAnuncio) references final_anuncio(idAnuncio)
);

CREATE TABLE final_favorito(
	idUser INT(10) UNSIGNED NOT NULL,
	idAnuncio INT(10) UNSIGNED NOT NULL,
	CONSTRAINT PK_comentario PRIMARY KEY (idUser,idAnuncio),
	CONSTRAINT FK_favorito_usuario FOREIGN KEY (idUser) references final_usuario(idUser),
	CONSTRAINT FK_favorito_anuncio FOREIGN KEY (idAnuncio) references final_anuncio(idAnuncio)	
);

CREATE TABLE final_imagen(
	idAnuncio INT(10) UNSIGNED NOT NULL,
	original VARCHAR(100) NOT NULL,
	big VARCHAR(100) NOT NULL,
	medium VARCHAR(100) NOT NULL,
	small VARCHAR(100) NOT NULL,
	CONSTRAINT PK_imagen PRIMARY KEY (idAnuncio),
	CONSTRAINT FK_imagen_anuncio FOREIGN KEY (idAnuncio) references final_anuncio (idAnuncio)
);