#!/bin/bash

# Usage: set-static-ip.sh <address> <gateway>

# Check if correct number of arguments are passed
if [ "$#" -ne 3 ]; then
    echo "Usage: set-static-ip.sh <address> <gateway> <mask (24,17,...)>"
    exit 1
fi

# Variables for new static IP and gateway
STATIC_IP=$1
GATEWAY=$2
MASK=$3

echo "Updating network settings..."

echo "network:
  version: 2
  renderer: networkd
  ethernets:
    enp0s3:
      dhcp4: no
      addresses:
        - ${STATIC_IP}/${MASK}  # Adresse IP statique et masque de réseau
      routes:
        - to: default
          via: ${GATEWAY}  # Adresse de la passerelle
      nameservers:
        addresses:
          - 8.8.8.8  # Google DNS
          - 8.8.4.4" > /etc/netplan/01-static.yaml

if [ -f /etc/netplan/02-dhcp.yaml ]; then
  echo "Switching to Static IP..."
  sudo mv /etc/netplan/02-dhcp.yaml /etc/netplan/02-dhcp.yaml.disabled
  sudo rm -f /etc/netplan/01-static.yaml.disabled
else
  echo "network:
    ethernets:
        enp0s3:
            dhcp4: true
    version: 2" > /etc/netplan/02-dhcp.yaml.disabled
  sudo chmod 600 /etc/netplan/02-dhcp.yaml.disabled
  sudo rm -f /etc/netplan/01-static.yaml.disabled
fi

sudo chmod 600 /etc/netplan/01-static.yaml

sudo netplan apply