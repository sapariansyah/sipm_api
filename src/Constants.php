<?php

/* REQUEST TYPE */
const REQ_TYPE_EMAIL = 'email';
const REQ_TYPE_NUMERIC = 'numeric';
const REQ_TYPE_TEXT = 'text';

/* REQUEST TYPE */
const DATA_TYPE_INTEGER = 'integer';
const DATA_TYPE_DECIMAL = 'decimal';
const DATA_TYPE_STRING = 'string';


/* ERROR CODE */
const ERR_SERVER_SUCCESS = 1;
const ERR_SERVER_ERROR = 2;
const ERR_SERVER_TOKEN_NOT_FOUND = 3;
const ERR_SERVER_TOKEN_NOT_VALID = 4;
const ERR_SERVER_API_KEY_NOT_FOUND = 5;
const ERR_SERVER_API_KEY_NOT_VALID = 6;
const ERR_SERVER_REQ_HEADER_NOT_VALID = 7;
const ERR_SERVER_REQUEST_TIMEOUT = 8;

/* LOGGER TYPE */
const LOGGER_INFO = 1;
const LOGGER_WARNING = 2;
const LOGGER_ERROR = 3;

/* GROUP ROUTE */
const GROUP_ROUTE_AUTH = 'auth';

/* HTTP HEADER */
const HTTP_HEADER_ACCESS_TOKEN = 'Access-Token';
const HTTP_HEADER_SIGNATURE = 'Signature';
const HTTP_HEADER_API_KEY = 'Api-Key';
const HTTP_HEADER_TIMESTAMP = 'Timestamp';

/* HTTP REQUEST ATTRIBUTE */
const HTTP_REQ_ATT_USER_ID = 'user_id';

/* API KEY */
const API_KEY_PUBLIC = 'papank070989';

/* REQUEST TIMESTAMP THRESHOLD */
const TIMESTAMP_REQ_MIN_THRESHOLD = 15;
const TIMESTAMP_REQ_MAX_THRESHOLD = 15;

/* USER STATUS */
const USER_STATUS_INACTIVE = 0;
const USER_STATUS_ACTIVE = 1;

/* */
const ROLE_PUBLIC = 0;
const ROLE_INVESTOR = 1;
const ROLE_STAFF = 2;
const ROLE_REVIEWER = 3;
const ROLE_TU_DIREKTUR = 4;
const ROLE_TU_DEPUTI = 5;