const express = require('express');
const router = express.Router();
const Notifications = require('../models/Notification');
const Favorites = require('../models/Favorites');



//get movies
router.post('/', async (req, res) => {
    console.log(req.body);
    // console.log(req.body.User_id);
    console.log(req.body.data[0].ID.value);
    // console.log(req.body.data[0].id.value);//prosthesa
    console.log(req.body.subscriptionId);
    try {
        const favorites =await Favorites.find({Movie_id:req.body.data[0].ID.value});// vriskw ola ta favorites me to sigekrimeno movie id
        // favorites.forEach(fav => {
            console.log(favorites);
        await Promise.all(favorites.map(async (fav) => { // kai gia kathe favorite me auto to movie id ftiaxnw ena notification gia to kathe user id
            // opote o kathe user 8a exei kai diko tou notification (gia na mporoume na to svinoume kiolas)
            console.log( "Movie with" + req.body.data[0].ID.value + "ID changed");
            console.log(fav.User_id);
            console.log(req.body.subscriptionId);


            const notify = new Notifications({
                Text: "Movie with " + req.body.data[0].ID.value + " ID changed",
                User_id: fav.User_id,
                Movie_id:req.body.data[0].ID.value,
                Sub_id: req.body.subscriptionId
            });
            console.log(notify);
            const savedNotification = await notify.save();


        }));

        // console.log(movies);
        res.json(savedNotification);
    } catch (err) {

        res.json({
            message: err
        });
    }
});

router.post('/notify', async (req, res) => {
    //   console.log(req.body);
    console.log(req.body.User_id);
    //   console.log(req.body);
    try {
        const notify =await Notifications.find({User_id:req.body.User_id});// gurizei ena pinaka apo text, 
        //an den paizei vgale to .Text kai 8a gurizei array me {"Text":"mpla mpla","User_id":".."}

        console.log(notify);
        res.json(notify);
    } catch (err) {
        res.json({
            message: err
        });
    }
});

router.post('/deleteNotif', async(req, res) => { // delete all notification by user id
    //   console.log(req.body);
    console.log(req.body.User_id);
    //   console.log(req.body);
    try {
        const notify = await Notifications.deleteMany({ User_id: req.body.User_id });

        res.json({
            message: "Notif Deleted"
        });
    } catch (err) {

        res.json({
            message: err
        });
    }
});

router.post('/subId', async(req, res) => { // delete all subs by movie id
    //   console.log(req.body);
    console.log(req.body.Movie_id);
    //   console.log(req.body);
    try {
        const notify = await Notifications.findOne({ Movie_id: req.body.Movie_id });

        res.json(notify);
    } catch (err) {

        res.json({
            message: err
        });
    }
});



module.exports = router;