const mongoose = require('mongoose');

const PostSchema =mongoose.Schema({
Title:{
    type: String,
    required: true
},
Start_date:{
    type: Date,
    required: false
},
End_date:{
    type: Date,
    required: false
},
Cinema:{ 
    type: String,
    required: false
 },
Category:{ 
    type: String,
    required: true
}

});


module.exports = mongoose.model('Movies',PostSchema);
