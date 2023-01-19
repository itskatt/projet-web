create table if not exists client (
    email varchar(30),
    password_ varchar(255) not null,
    last_name varchar(30) not null,
    first_name varchar(30) not null,
    admin_ boolean not null,
    primary key (email)
);

create table if not exists session (
    id int auto_increment,
    client_email varchar(30),
    token varchar(50),
    expires datetime,
    primary key (id),
    foreign key (client_email) references client(email)
);

create table if not exists cart (
    id int auto_increment,
    client_email varchar(30),
    primary key (id),
    foreign key (client_email) references client(email)
);

create table if not exists invoice (
    id int auto_increment,
    cart_id int,
    created datetime not null,
    primary key (id),
    foreign key (cart_id) references cart(id)
);

create table if not exists supplier (
    id int auto_increment,
    name_ varchar(30),
    primary key (id)
);

create table if not exists article (
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
    foreign key (supplier_id) references supplier(id)
);

create table if not exists cart_article (
    cart_id int,
    article_id int,
    quantity int default 0,
    primary key (cart_id, article_id),
    foreign key (cart_id) references cart(id) on delete cascade,
    foreign key (article_id) references article(id) on delete cascade
);

create table if not exists stock (
    id int auto_increment,
    article_id int,
    quantity int default 0,
    primary key (id),
    foreign key (article_id) references article(id)
                                 on delete cascade
);