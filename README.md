# aboutMe

## 使用 Docker 部署

準備 `docker-compose.yml`(專案根目錄已提供範例),於同目錄執行：

```bash
docker compose up -d
```

首次啟動會自動完成資料庫遷移、建立初始管理員帳號（帳密僅於首次啟動的日誌中顯示一次，可用 `docker compose logs app` 查看），並持久化資料於 Docker volume。

映像檔會在合併至 `main` 分支後自動建置並推送到 [Docker Hub](https://hub.docker.com/r/meykatoe/aboutme)。
