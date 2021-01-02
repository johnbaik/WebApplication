FROM node:12.18.1

RUN mkdir -p /usr/src/app 

WORKDIR /usr/src/app 
 
COPY package*.json ./
# COPY ./rest/package-lock.json package-lock.json
 
RUN npm install -g nodemon
RUN ls -al
COPY . .

EXPOSE 5000

CMD [ "npm", "start" ]

