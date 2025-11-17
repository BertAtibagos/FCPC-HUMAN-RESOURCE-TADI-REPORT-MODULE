async function totalStats(){
    try{
        const request = await fetch(``)

        const result = await request.json();
    }
    catch(error){
        console.log("ERROR: ", error)
    }
}