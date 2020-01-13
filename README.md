1. Run \
**composer install**

2. Copy and rename **.env.example** to **.env**

3. Run \
**docker-compose up**

4. Update settings in **.env** \
**docker-compose exec php vim .env**

GITHUB_AUTH_TOKEN - github access token \
GITHUB_USERS_LIMIT - users limit, because of the github request limitation

4. Open in browser \
http://127.0.0.1:8080/api/users \
http://127.0.0.1:8080/api/users?q=query
