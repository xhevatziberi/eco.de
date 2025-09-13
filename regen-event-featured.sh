#!/usr/bin/env bash
wp post list --post_type=event --post_status=publish --posts_per_page=-1 --field=ID \
| tr -d '\r' \
| while read -r PID; do
  TID=$(wp post meta get "$PID" _thumbnail_id | tr -d '\r')
  if [[ "$TID" =~ ^[0-9]+$ ]]; then
    echo "Post $PID → attachment $TID"
    wp media regenerate "$TID" --yes --image_size=events-small
  else
    echo "Post $PID has no featured image"
  fi
done
