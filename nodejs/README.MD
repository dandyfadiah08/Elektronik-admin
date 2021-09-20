**To run nodejs server**
- Make sure to update **.env** file
- For running in localhost, run
```
npm start
```
- For running in servers, please use **pm2**, run
```
pm2 start app.js
```

**About pm2**
- To install **pm2** and make it globals
```
npm i -g pm2
```
- To make **pm2** autostartup
```
pm2 startup
```

**To create API Documentation with redoc-cli**

- run the following code in terminal
```
redoc-cli bundle -o ./api-docs/filename.html ./api-docs/filename.json
```
For example
```
redoc-cli bundle -o ./api-docs/app-2.html ./api-docs/app-2.json
```