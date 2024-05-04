var voteData={
    totalVotes : 4,
    terms : [
        {
        'term' : 'whatevs',
        'votes' : 1,
        'ipAddresses' : "127.0.0.1",
        'cheekyIpAddresses' : []
        },
        
         {
        'term' : 'whatevahs2',
        'votes' : 3,
        'ipAddresses' : [],
        'cheekyIpAddresses' : []
        } 
    ]
}

var url = 'https//patrickmjdh.reclaimhosting.com';
var url = 'http://localhost/Hwaet';
    
var ipAddress=ipAddress;

function setIpAddress(ipAddress) {
    ipAddress=ipAddress;
}

function lookupIpAddress() {
          // Send a request to an external service to get the IP address
      fetch('https://api.ipify.org?format=json')
        .then(response => response.json())
        .then(data => {
          setIpAddress(data.ip); // Pass the IP address to the callback function
          console.log(data.ip);
        })
        .catch(error => {
          console.error('Error fetching IP address:', error);
   //       callback(null); // Pass null if there's an error
        });
}



function fetchVotes() {
// fetch to server
// needs to run on a timeOut
// needs to display signal that it's reloading

response = fetch(url + '?votes')
            .then(votesData => response.json())
            .catch (error => {
                console.error('Ooops no IP');
            }
          )
}


function checkCheekyDevils() {
// look at the data    
    
}

function displayCheekyDevil(cheekyVoteData) {
    
}

function sendVote(upvoteData) {
// fetch POST to server   

  try {
    const response = fetch(url + '?upvote', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json'
      },
      body: JSON.stringify(data)
    });
    if (!response.ok) {
      throw new Error('Network response was not ok');
    } else {
        renderPage();
    }
  } catch (error) {
    console.error('Error:', error);
    throw error;
  }
}


function createTerm(termData) {
    // fetch POST to server

  try {
    const response = fetch(url + '?createTerm', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json'
      },
      body: JSON.stringify(data)
    });
    if (!response.ok) {
      throw new Error('Network response was not ok');
    } else {
        renderPage();
    }
  } catch (error) {
    console.error('Error:', error);
    throw error;
  }
}


function setWidths() {
    //global response
    //calculate percentages for each term -- needs the response to have totalTerms and totalVotes
    //roll through terms, create as needed, and set the width CSS property
    termElements = document.getElementsByClassName('termElement');
    let totalVotes = voteData.terms.count;
    for(const term of voteData.terms) {
        termPercentage = term.votes/voteData.totalVotes;
        //termElement = documentGetElementById(term);
        //termElement.setAttribute('width', termPercentage);
        console.log(termPercentage);
    }
}

function addTermElement() {

}

function renderPage() {
    setIpAddress();
    fetchVoteData();
    sortTerms();
    checkCheekyDevil();
    renderTerms();
}

renderPage();