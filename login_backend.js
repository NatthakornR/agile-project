const { MongoClient } = require("mongodb");

// Replace the uri string with your connection string.
const uri = "mongodb+srv://mickldes1:YszYZVUcVPj5nRoN@etender.xmhjj.mongodb.net/?retryWrites=true&w=majority&appName=etender";


const client = new MongoClient(uri);

async function run() {
  try {
    const db = client.db('E-tender');
	const users = db.collection('Users');
	const query = { abc: 'haha' };
	users.insertOne(query);
	console.log(users.find());
  } finally {
    await client.close();
  }
}
run().catch(console.dir);