#!/usr/bin/env bash
# wait-for-it.sh
# A simple script to wait for a host:port to be available
host="$1"
shift
port="$1"
shift

until nc -z "$host" "$port"; do
  echo "Waiting for $host:$port..."
  sleep 1
done

exec "$@"
