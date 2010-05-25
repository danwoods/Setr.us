/**Uploader Object Prototype**/
Upload.prototype = { 
   numOfSets: 1,
   numOfSongs: 1,
   numOfEncores: 0, 
   artist: "Unknown",
   day: 0,
   month: 0,
   year: 0,
   showNotes: "",
   city: "",
   state: "",
   venue: "",
   taper: "Unknown",
   micLoc: "",
   source: "Unknown",
   lineage: "Unknown",
   masteredBy: "Unknown",
   getInfo: function() { 
     return 'Rating: ' + this.artist + ', price: ' + this.state; 
   }
};
