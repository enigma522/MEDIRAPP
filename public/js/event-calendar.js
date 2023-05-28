function transformObject(jsonObj) {
  const monthNames = [
    "January",
    "February",
    "March",
    "April",
    "May",
    "June",
    "July",
    "August",
    "September",
    "October",
    "November",
    "December",
  ];

  const transformedArray = [];

  jsonObj.forEach((obj) => {
    const id = obj.id.toString(); // convert the id to string
    const name = obj.title;
    const dateObj = new Date(obj.dueDate);
    const date =
      monthNames[dateObj.getMonth()] +
      " " +
      dateObj.getDate() +
      ", " +
      dateObj.getFullYear();
    const description = obj.description;
    const type = obj.type ? obj.type : "event";

    transformedArray.push({ id, name, date, description, type });
  });

  return transformedArray;
}


async function fetchEvents() {
  var myHeaders = new Headers();

  var requestOptions = {
    method: "GET",
    headers: myHeaders,
    redirect: "follow",
  };

  try {
    const response = await fetch(
      "http://127.0.0.1:8000/calendar/getAll/"+document.getElementById('iduser').value,
      requestOptions
    );
    const result = await response.json();
    return result;
  } catch (error) {
    console.log("error", error);
  }
}

async function getEventById(id) {
  var myHeaders = new Headers();
  myHeaders.append("Content-Type", "application/json");

  var requestOptions = {
    method: "GET",
    headers: myHeaders,
    redirect: "follow",
  };
  try {
    const response = await fetch(
      "http://127.0.0.1:8000/calendar/find/" + id,
      requestOptions
    );
    const result = await response.json();
    return result;
  } catch (error) {
    console.log("error", error);
  }
}

async function modEventType(eventJson) {
  var myHeaders = new Headers();
  myHeaders.append("Content-Type", "application/json");
  myHeaders.append("Cookie", "PHPSESSID=rmest7rogq6dv7egoemsv2096v");
  const type = eventJson.type == "event" ? "birthday" : "event";
  const eventJsonMod = {
    id: eventJson.id,
    title: eventJson.title,
    description: eventJson.description,
    dueDate: eventJson.dueDate,
    type: type,
  };
  var raw = JSON.stringify(eventJsonMod);

  var requestOptions = {
    method: "POST",
    headers: myHeaders,
    body: raw,
    redirect: "follow",
  };
  try {
    const response = await fetch(
      "http://127.0.0.1:8000/calendar/" + eventJson.id + "/edit",
      requestOptions
    );
    const result = await response.json();
    return result;
  } catch (error) {
    console.log("error", error);
  }
}

// initialize your calendar, once the page's DOM is ready
async function doCalendar() {
  const events = await fetchEvents();
  console.log(events);
  const ModEvents = transformObject(events);
  $(document).ready(function () {
    $("#calendar").evoCalendar({
      theme: "Royal Navy",
      sidebarToggler: true,
      sidebarDisplayDefault: false,
      eventDisplayDefault: true,
    });
    ModEvents ? ($events = ModEvents) : ($events = {});

    $("#calendar").evoCalendar("addCalendarEvent", $events);
  });
}

const even = document.querySelector("#calendar");

if (even) {
  even.addEventListener("click", async (e) => {
    const x = e.target.parentNode.parentNode.getAttribute("data-event-index");

    const icon = e.target.parentNode.previousElementSibling.firstElementChild;
    if (e.target.className == "event-title") {
      icon.classList.toggle("event-bullet-birthday");
      icon.classList.toggle("event-bullet-event");
    }
    if (x) {
      const todoEvent = await getEventById(x);
      const modifiedEvent = await modEventType(todoEvent);
    }
  });
}

doCalendar();