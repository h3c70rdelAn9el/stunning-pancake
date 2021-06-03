<template>
<div class="text-gray-100 mb-9">
  <!-- PLACES -->
  <div class="text-gray-800 places-input">
    <input type="search" id="address" placeholder="City, STATE" class="w-full bg-blue-800 rounded" />
    <p>
    </p>
  </div>

  <!-- DAILY WEATHER -->
  <div class="max-w-lg mx-auto mt-4 overflow-hidden font-sans bg-gray-900 rounded-lg shadow-xl weather-container w-128">
    <h1 class="mt-3 ml-6 text-3xl" id="address-value">
      {{ location.name }}
      <!-- {{ location.place }} -->
      <span class="ml-1 text-lg">{{ location.country }}</span>
    </h1>
    <div class="flex items-center justify-between px-6 py-8 current-weather">
      <div class="flex item-center">
        <div>
          <div class="text-6xl font-semibold">
            {{ currentTemp.actual }}
            <span class="text-4xl">°F</span>
          </div>
          <div class="text-xs">
            feels like
            <span class="ml-2 text-lg">{{ currentTemp.feels }}°F</span>
          </div>
        </div>
        <div class="mx-5 mt-10 text-sm">
          <div class="font-semibold">
            {{ currentTemp.description }}
          </div>
        </div>
      </div>
      <div class="icon">
        <img :src="currentTemp.icon" />
      </div>
    </div>
    <!-- EXTENDED FORECAST  -->
    <div>
      <button class="mb-2 ml-4 border border-blue-200 rounded" @click="isHidden = !isHidden">
        <p class="p-1 text-blue-200 hover:bg-blue-500">Show Extended</p>
      </button>
      <div v-show="!isHidden" class="px-6 py-8 overflow-hidden text-sm bg-blue-700 future-weather">
        <div class="flex items-center" v-for="(day, index) in daily" :key="day.dt" :class="{ 'mt-8': index > 0 }">
          <div class="w-1/6 text-lg text-gray-200">
            {{ toWeekDay(day.dt) }}
          </div>
          <div class="flex flex-row items-center w-4/6 px-4">
            <div>
              <img :src="forecastIcon(day.weather[0].icon)" />
            </div>
            <div>
              {{ day.weather[0].description }}
            </div>
          </div>
          <div class="w-1/6 text-right">
            <div>
              high {{ Math.round(day.temp.max) }}°
              <span class="text-xs">°F</span>
            </div>
            <div>
              low {{ Math.round(day.temp.min) }}°
              <span class="text-xs">°F</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
</template>

<script>
export default {
  data() {
    return {
      currentTemp: {
        actual: "",
        feels: "",
        description: "",
        icon: "",
        wind: ""
      },
      location: {
        place: "",
        name: "Glendale",
        country: "",
        lat: 34.1425,
        lon: -118.2551
      },
      daily: [],
      isHidden: true,
    };
  },

  watch: {
    location: {
      handler(newValue, oldValue) {
        this.fetchdata();
      },
      deep: true
    }
  },
  mounted() {
    this.fetchdata();
    var placesAutocomplete = places({
      appId: "plKN3WVHPS46",
      apiKey: "8cc1498640c8e77d977e2d1684253e51",
      container: document.querySelector("#address")
    });

    var $address = document.querySelector("#address-value");
    placesAutocomplete.on("change", e => {
      $address.textContent = e.suggestion.value;
      console.log(e);
      this.location.name = `${e.suggestion.name}, ${e.suggestion.country}`;
      this.location.lat = e.suggestion.latlng.lat;
      this.location.lon = e.suggestion.latlng.lng;
    });

    placesAutocomplete.on("clear", function () {
      $address.textContent = "none";
    });
  },

  methods: {
    fetchdata() {
      fetch(`/api/?lat=${this.location.lat}&lon=${this.location.lon}`)
        .then(response => response.json())
        .then(data => {
          this.currentTemp.actual = Math.round(data.main.temp);
          this.currentTemp.feels = Math.round(data.main.feels_like);
          this.currentTemp.description = data.weather[0].description;
          this.currentTemp.icon = `http://openweathermap.org/img/wn/${data.weather[0].icon}@2x.png`;
          this.currentTemp.wind = data.wind.speed;
          this.location.place = data.name;
          this.location.name = data.name;
          this.location.country = data.sys.country;
          console.log(data);
        });

      fetch(`/api/forecast?lat=${this.location.lat}&lon=${this.location.lon}`)
        .then(response => response.json())
        .then(data => {
          this.daily = data.daily;
        });
    },
    toWeekDay(timestamp) {
      const newDate = new Date(timestamp * 1000);
      const day = ["SUN", "MON", "TUE", "WED", "THU", "FRI", "SAT"];
      return day[newDate.getDay()];
    },
    forecastIcon(icon) {
      let iconId = icon;
      return `http://openweathermap.org/img/wn/${iconId}@2x.png`;
    }
  }
};
</script>
