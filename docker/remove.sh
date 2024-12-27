#!/bin/bash

# This script is part of a Docker Compose examples repository.
# Author: Anton Panfilov <anton@panfilov.biz>
# Repository: https://github.com/anton-panfilov/docker-tmp

source .env

HL_EMOJI="🔴"
HL_BOLD_UNDERLINE="\033[1;4m"
HL_PURPLE="\033[45m"
HL_ERROR="\033[30;41m"
HL_END="\033[0m"
ERROR_EMOJI="⚠️"
ERROR_TEXT_NO_PROJECT="Error: PROJECT variable is not set in the .env file."
REMOVE_TEXT="Removing all containers and volumes for project"
CONFIRMATION_QUESTION_TEXT="Are you sure you want to proceed?"
CONFIRMATION_CANCEL_TEXT="Operation canceled."

if [[ -z "$PROJECT" ]]; then
  echo -e "${ERROR_EMOJI} ${HL_ERROR}${ERROR_TEXT_NO_PROJECT}${HL_END}"
  exit 1
fi

echo -e "${HL_EMOJI} ${HL_BOLD_UNDERLINE}${REMOVE_TEXT}: ${HL_PURPLE}${PROJECT}${HL_END}"

read -p "${CONFIRMATION_QUESTION_TEXT} (Y/N): " CONFIRM
if [[ "$CONFIRM" != "Y" && "$CONFIRM" != "y" ]]; then
  echo -e "${CONFIRMATION_CANCEL_TEXT}"
  exit 0
fi

docker compose -p "$PROJECT" down --volumes --remove-orphans
echo -e ""