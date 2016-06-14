#!/bin/sh

ANSIBLE_ROOT=$(dirname $0)
ANSIBLE_INVENTORY=$ANSIBLE_ROOT/inventories/prod-host
ANSIBLE_PLAYBOOK=$ANSIBLE_ROOT/cloud-deploy.yml
COMMIT_HASH=$(git fetch upstream && git rev-parse --verify upstream/master)

ansible-playbook -vv -i $ANSIBLE_INVENTORY $ANSIBLE_PLAYBOOK --extra-vars="commit_hash=$COMMIT_HASH"
