const express = require('express');
const router = express.Router();

const Movies= require('../models/Movies');
const Favorites= require('../models/Favorites');

//get movies
router.post('/',async (req,res)=>{
    try{
    const movies= await Movies.find( req.body );
        console.log(movies);
        res.json(movies);
    }catch(err){

        res.json({
            message: err 
        });
    }

});

//add to favorites
router.post('/addToFavorites',async (req,res)=>{
    console.log(req.body);

    try{
        const fav= await Favorites.find( req.body );
        if(fav.length>=1){
            res.json({
                message: "Already in Favorites"
            });
        }
        else{
            const favorite =new Favorites({
                User_id:req.body.User_id,
                Movie_id:req.body.Movie_id
            });
            const savedFavorite=await favorite.save();
            res.json({
                message: "Added to Favorites"
            });
        }
    }catch(err){
        console.log(err);

        res.json({
            message: "error" 
        });
    }

});

module.exports = router;