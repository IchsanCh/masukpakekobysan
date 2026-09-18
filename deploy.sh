# auto update CI/CD
set -e

echo ">> git pull"
git pull

echo ">> build image app (masukpakeko-app:latest)"
docker compose build masukpakeko

echo ">> recreate container app biar pake image baru"
docker compose up -d --force-recreate masukpakeko

echo ">> build ulang asset frontend di dalam container app yang jalan"
docker compose exec masukpakeko npm run build

echo ">> restart container lain (queue, scheduler & nginx) biar ikut lihat kode/asset terbaru"
docker compose restart queue scheduler webmasukpakeko

echo ">> selesai. cek log kalau perlu:"
echo "   docker compose logs -f masukpakeko"
echo "   docker compose logs -f queue"
echo "   docker compose logs -f scheduler"
echo "   docker compose logs -f webmasukpakeko"