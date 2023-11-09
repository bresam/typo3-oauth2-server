CREATE TABLE tx_oauth2server_domain_model_client
(
    identifier           varchar(32)  DEFAULT ''  NOT NULL,
    name                 varchar(255) DEFAULT ''  NOT NULL,
    secret               varchar(100) DEFAULT ''  NOT NULL,
    frontend_user_groups int(11)      DEFAULT '0' NOT NULL,
    redirect_uris        text,
    description          text
);

CREATE TABLE tx_oauth2server_domain_model_client_fe_user_group_mm
(
    uid_local       int(11) DEFAULT '0' NOT NULL,
    uid_foreign     int(11) DEFAULT '0' NOT NULL,
    sorting         int(11) DEFAULT '0' NOT NULL,
    sorting_foreign int(11) DEFAULT '0' NOT NULL,
    KEY uid_local (uid_local),
    KEY uid_foreign (uid_foreign)
);

CREATE TABLE tx_oauth2server_domain_model_accesstoken
(
    identifier       varchar(255) DEFAULT '' NOT NULL,
    user_identifier  varchar(255) DEFAULT NULL,
    scopes           varchar(255) DEFAULT NULL,
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

CREATE TABLE tx_oauth2server_domain_model_authorizationcode
(
    identifier       varchar(255) DEFAULT '' NOT NULL,
    redirect_uri     varchar(255) DEFAULT NULL,
    user_identifier  varchar(255) DEFAULT NULL,
    scopes           varchar(255) DEFAULT NULL,
    client           int unsigned DEFAULT NULL,
    expiry_date_time int unsigned DEFAULT NULL,
    nonce            varchar(255) DEFAULT NULL
);
