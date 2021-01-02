const express = require('express');
const router = express.Router();

const Cinema= require('../models/Cinema');
const Movies= require('../models/Movies');
const Favorites= require('../models/Favorites');

// const Cinema= require('../models/Cinema');


//add Cinema
router.post('/addCinema',async (req,res)=>{

    const cinemas= await Cinema.find( req.body );
        if(cinemas.length>=1){
            res.json({
                message: "Already in Cinemas"
            });
        }
        else{
            try{
                const cinema =new Cinema({
                    ID: req.body.ID,   
                    Cinema_owner: req.body.Cinema_owner,
                    Cinema_name:req.body.Cinema_name
            
                });
                const savedCinema=await cinema.save();
                res.json({
                    message: "Cinema added" 
                });
            }
            catch(err){
                res.json({
                    message: err 
                });
            }
        }
        //console.log(res);
});

router.post('/showCinemas',async (req,res)=>{

            try{
                const cinemas= await Cinema.find( req.body );
                if(cinemas.length>=1){
                    res.json(cinemas);
                }
            }
            catch(err){
                res.json({
                    message: err 
                });
            }
        
        //console.log(res);
});

//delete cienma
router.delete('/deleteCinema',async (req,res)=>{
    try{
        const cinema= await Cinema.deleteOne( req.body );
        console.log("Deleted Successfully "+cinema['n']+" cinemas");
        res.json({
            message: "deleted" 
        });
    }
    catch(err){
        res.json({
            message: err 
        });
    }
});


//add movie
router.post('/addMovie', async (req, res) => {
    const movie = new Movies({

        Title: req.body.Title,
        Start_date: req.body.Start_date,
        End_date: req.body.End_date,
        Cinema: req.body.Cinema,
        Category: req.body.Category
    });
    try {
        const savedMovie = await movie.save();
        res.json({
            message: "Added",
            movieId: savedMovie._id
        });
    }
    catch (err) {
        res.json({
            message: err
        });
    }
});


//delete movie
router.delete('/deleteMovie',async (req,res)=>{
    try{
        const movies= await Movies.deleteOne( req.body );
        console.log("Deleted Successfully "+movies['n']+" movies");
        // res.json(movies);
        res.json({
            message: "Deleted"
        });
    }
    catch(err){
        res.json({
            message: err 
        });
    }
});

//update movie
router.patch('/updateMovie',async (req,res)=>{
    try{
        const filter = { _id: req.body._id };
        const update = req.body;
        //console.log(req.body);
        let updatedMovie = await Movies.findOneAndUpdate(filter, update, {new: true});
        //console.log(req.body);
        //console.log("Updated Successfully \n"+updatedMovie);
        // res.json(updatedMovie);
        res.json({
            message: "Updated"
        });
    }
    catch(err){
        res.json({
            message: err 
        });
    }
});

router.post('/movies',async (req,res)=>{

    var owner =req.body.Cinema_owner;
    console.log(owner);

    try{
    //const movies= await Movies.find( req.body );
    const cinemas= await Cinema.find( {Cinema_owner:owner} );
    var results=[];
   // console.log(cinemas);

    await Promise.all(cinemas.map (async (cin)  => {
        const movies= await Movies.find( {Cinema:cin.Cinema_name});
       // console.log(movies);

        if(movies!="")
        results.push(movies);

      }));
      res.json(results);

    console.log(results);
        //res.json(movies);
    }catch(err){

        res.json({
            message: err 
        });
    }

});


module.exports = router;