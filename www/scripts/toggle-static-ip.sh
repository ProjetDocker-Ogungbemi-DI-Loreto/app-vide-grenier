#!/bin/bash

if [ -f /etc/netplan/02-dhcp.yaml ]; then
  echo "Switching to Static IP..."
  sudo mv /etc/netplan/02-dhcp.yaml /etc/netplan/02-dhcp.yaml.disabled
  sudo mv /etc/netplan/01-static.yaml.disabled /etc/netplan/01-static.yaml
else
  echo "Switching to DHCP..."
  sudo mv /etc/netplan/01-static.yaml /etc/netplan/01-static.yaml.disabled
  sudo mv /etc/netplan/02-dhcp.yaml.disabled /etc/netplan/02-dhcp.yaml
fi

sudo netplan apply