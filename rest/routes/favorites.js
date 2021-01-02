const express = require('express');
const router = express.Router();

const Movies= require('../models/Movies');
const Favorites= require('../models/Favorites');

//get movies
router.post('/',async (req,res)=>{
    console.log(req.body);

    try{
        const favorites= await Favorites.find({User_id:req.body.User_id});
        console.log(favorites);
        var results=[];
        await Promise.all(favorites.map (async (fav)  => {
            const movie= await Movies.findById(fav.Movie_id);
            console.log(movie);
            //tmp.JSON.parse(movie);

            results.push(movie);

    }));
    res.json(results);

    }catch(err){
        res.json({
            message: err 
        });
    }

    //console.log(res);

});



//delete FAVORITE
router.delete('/deleteFavorite',async (req,res)=>{
    try{
        const favorite= await Favorites.deleteOne( {
            Movie_id:req.body.Movie_id,
            User_id:req.body.User_id} );
        console.log("Deleted Successfully "+favorite['n']+" favorites");
        res.json(favorite);
    }
    catch(err){
        res.json({
            message: err 
        });
    }
});




module.exports = router;