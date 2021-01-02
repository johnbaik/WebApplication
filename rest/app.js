const express = require ('express');
const app= express();
const mongoose= require('mongoose');
const port = process.env.PORT || 5000;

const bodyParser = require('body-parser');

app.use(bodyParser.json());

//import routes
const moviesRoute= require('./routes/movies');
const ownerRoute= require('./routes/owner');
const favoritesRoute= require('./routes/favorites');
const notificationsRoute= require('./routes/notifications');

//const usersRoute= require('./routes/users');

app.use('/movies',moviesRoute);
app.use('/owner',ownerRoute);
app.use('/favorites',favoritesRoute);
app.use('/notifications',notificationsRoute);

//routes

app.get('/',(req,res)=>{
    res.send('home');
 
});

// app.post('/test', (req, res) => {
//   console.log("kati");
//   console.log(req.body);
//   console.log(req.body.data[0].Category.value);
//   // res.send(req.body);
// });



//Connect to database
mongoose
  .connect('mongodb://mongo_db:27017', {
    useUnifiedTopology: true,
    useNewUrlParser: true,
  })
  .then(() => console.log('MongoDB Connected...'))
  .catch(err => console.log(err));

//listeners
app.listen(port, function () {
  console.log(`Rest Api listening on ${port}!`);

});
