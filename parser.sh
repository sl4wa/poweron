#!/usr/bin/env bash
curl -s 'https://power-api.loe.lviv.ua/api/pw_accidents?pagination=false&otg.id=28&city.id=693' \
  | jq -r '
    .["hydra:member"][]
    | "\(.dateEvent)\t\(.datePlanIn)\t\(.city.name)\t\(.street.id)\t\(.street.name)\t\(.buildingNames)\t\(.koment)"
  '
