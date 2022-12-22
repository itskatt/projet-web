create table if not exists Client (
    email varchar(30),
    password_ varchar(255) not null,
    last_name varchar(30) not null,
    first_name varchar(30) not null,
    admin_ boolean not null,
    primary key (email)
);

create table if not exists Session (
    id int auto_increment,
    client_email varchar(30),
    token varchar(50),
    expires datetime,
    primary key (id),
    foreign key (client_email) references Client(email)
);

create table if not exists Cart (
    id int auto_increment,
    client_email varchar(30),
    primary key (id),
    foreign key (client_email) references Client(email)
);

create table if not exists Invoice (
    id int auto_increment,
    cart_id int,
    created date not null,
    primary key (id),
    foreign key (cart_id) references Cart(id)
);

create table if not exists Supplier (
    id int auto_increment,
    name_ varchar(30),
    primary key (id)
);

create table if not exists Article (
    id int auto_increment,
    supplier_id int,
    name_ varchar(30) not null,
    description_ text,
    rating int check (
        rating > -1
        and rating < 6
    ),
    year int not null,
    supplier_price decimal(20, 2),
    image_ varchar(23),
    primary key (id),
    foreign key (supplier_id) references Supplier(id)
);

create table if not exists Cart_Article (
    cart_id int,
    article_id int,
    quantity int default 0,
    primary key (cart_id, article_id),
    foreign key (cart_id) references Cart(id),
    foreign key (article_id) references Article(id)
);

create table if not exists Stock (
    id int auto_increment,
    article_id int,
    quantity int default 0,
    primary key (id),
    foreign key (article_id) references Article(id)
);