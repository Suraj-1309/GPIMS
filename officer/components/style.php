<style>
    .nav-link {
    font-size: 16px;
    text-decoration: none;
    color: #000;
}

/* General styling for sidebar items */
ul.nav .nav-item.nav-link {
    background-color: rgb(226, 226, 226);
    transition: background-color 0.3s;
}

ul.nav .nav-item.nav-link a {
    text-decoration: none;
    color: black;
    width: 100%;
    height: 100%;
    display: block;
    transition: color 0.3s;
}

/* Hover effect */
ul.nav .nav-item.nav-link:hover {
    background-color: rgb(57, 155, 234);
}

ul.nav .nav-item.nav-link:hover a {
    color: white;
}

/* Dropdown menu styling */
#receivedRRDropdown {
    padding-left: 10px;
    transition: background-color 0.3s;
}

/* Dropdown items */
#receivedRRDropdown .nav-item {
    transition: background-color 0.3s;
}

#receivedRRDropdown .nav-item .nav-link {
    color: black;
}

#receivedRRDropdown .nav-item:hover {
    background-color: rgb(57, 155, 234);
}

#receivedRRDropdown .nav-item:hover .nav-link {
    color: white;
}


#alloteRRDropdown {
    padding-left: 10px;
    transition: background-color 0.3s;
}

/* Dropdown items */
#alloteRRDropdown .nav-item {
    transition: background-color 0.3s;
}

#alloteRRDropdown .nav-item .nav-link {
    color: black;
}

#alloteRRDropdown .nav-item:hover {
    background-color: rgb(57, 155, 234);
}

#alloteRRDropdown .nav-item:hover .nav-link {
    color: white;
}

/* Logout button styling */
input[type="submit"] {
    width: 100%;
    text-align: left;
    background-color: #f8f9fa;
    border: none;
    padding: 10px 20px;
    font-size: 17px;
    font-weight: 900;
    cursor: pointer;
    color: black;
    text-decoration: none;
}

input[type="submit"]:hover {
    background-color: red;
    color: white;
}

/* Style for "All Record" */
a[data-toggle="collapse"] {
    display: block;
    background-color: rgb(226, 226, 226); /* Same as other items */
    padding: 10px;
    transition: background-color 0.3s, color 0.3s;
    color: black;
    text-decoration: none;
    width: 100%;
}

/* Hover effect for "All Record" */
a[data-toggle="collapse"]:hover {
    background-color: rgb(57, 155, 234);
    color: white;
}

    </style>