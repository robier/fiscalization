#!/bin/sh

# remove files
rm -f cert.p12 root.crt rootCACert.pem rootCAKey.pem

# generate test certificates
openssl genrsa -out rootCAKey.pem 2048

openssl req -x509 -sha256 -new -nodes -key rootCAKey.pem -days 9999 -out rootCACert.pem -subj "/C=HR/ST=Zagreb/L=Zagreb/O=Code Bending/OU=IT Department/CN=Robert Premar/emailAddress=robert.premar@gmail.com"

openssl x509 -in rootCACert.pem -text > root.crt

openssl pkcs12 -export -out cert.p12 -inkey rootCAKey.pem --in rootCACert.pem -passin pass:******** -passout pass:********

rm -f rootCACert.pem rootCAKey.pem
