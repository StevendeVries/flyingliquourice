#!/bin/sh

ANSIBLE_ROOT=$(dirname $0)
ANSIBLE_INVENTORY=$ANSIBLE_ROOT/inventories/prod-host
ANSIBLE_PLAYBOOK=$ANSIBLE_ROOT/cloud-infra.yml

ansible-playbook -vv -i $ANSIBLE_INVENTORY $ANSIBLE_PLAYBOOK
