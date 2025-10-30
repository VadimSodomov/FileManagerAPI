Перед запуском бэка нужно:
- создать файл `.env`,
- вставить туда содержимое файла `.env.dev`
- заполнить пароли к БД и JWT (любые)

Бэк нужно запускать из директории `backend`
Первый запуск:
```shell
docker compose up --build -d
```

Последующие запуски:
```shell
docker compose up -d
```

После этого API будет доступно по http://127.0.0.1:8080/api/.

После того, как закончили работу:
```shell
docker compose down
```

Фронт:
```shell
npm install ; npm run dev
```