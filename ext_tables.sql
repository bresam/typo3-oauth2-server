CREATE TABLE tx_oauth2server_domain_model_client
(
    identifier    varchar(32)  DEFAULT '' NOT NULL,
    name          varchar(255) DEFAULT '' NOT NULL,
    secret        varchar(100) DEFAULT '' NOT NULL,
    redirect_uris text,
    description   text
);

CREATE TABLE tx_oauth2server_domain_model_accesstoken
(
    identifier       varchar(255) DEFAULT '' NOT NULL,
    user_identifier  varchar(255) DEFAULT NULL,
    scopes           varchar(255) DEFAULT '' NOT NULL,
    client           varchar(32)  DEFAULT '' NOT NULL,
    expiry_date_time int unsigned DEFAULT NULL,
    revoked          int unsigned DEFAULT NULL
);

CREATE TABLE tx_oauth2server_domain_model_refreshtoken
(
    identifier       varchar(255) DEFAULT '' NOT NULL,
    access_token     varchar(255) DEFAULT '' NOT NULL,
    expiry_date_time int unsigned DEFAULT NULL
);
