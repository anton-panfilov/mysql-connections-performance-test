#!/bin/bash

# This script is part of a Docker Compose examples repository.
# Author: Anton Panfilov <anton@panfilov.biz>
# Repository: https://github.com/anton-panfilov/docker-tmp

source .env

HL_EMOJI="🟢"
HL_BOLD_UNDERLINE="\033[1;4m"
HL_PURPLE="\033[45m"
HL_ERROR="\033[30;41m"
HL_END="\033[0m"
ERROR_EMOJI="⚠️"
ERROR_TEXT_NO_PROJECT="Error: PROJECT variable is not set in the .env file."
UP_TEXT="Bringing up containers for"

if [[ -z "$PROJECT" ]]; then
  echo -e "${ERROR_EMOJI} ${HL_ERROR}${ERROR_TEXT_NO_PROJECT}${HL_END}"
  exit 1
fi

./stop.sh # stop before start

echo -e "${HL_EMOJI} ${HL_BOLD_UNDERLINE}${UP_TEXT}: ${HL_PURPLE}${PROJECT}${HL_END}"
docker compose -p ${PROJECT} build