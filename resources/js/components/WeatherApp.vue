<template>
<div class="text-gray-100 mb-9">
  <!-- PLACES -->
  <div class="places-input text-gray-800">
    <input type="search" id="address" placeholder="City, STATE" class="w-full bg-blue-800 rounded" />
    <p>
    </p>
  </div>

  <!-- DAILY WEATHER -->
  <div class="mx-auto rounded-lg weather-container font-sans w-128 max-w-lg overflow-hidden bg-gray-900 shadow-xl mt-4">
    <h1 class="text-3xl ml-6 mt-3" id="address-value">
      {{ location.name }}
      <!-- {{ location.place }} -->
      <span class="text-lg ml-1">{{ location.country }}</span>
    </h1>
    <div class="current-weather flex items-center justify-between px-6 py-8">
      <div class="flex item-center">
        <div>
          <div class="font-semibold text-6xl">
            {{ currentTemp.actual }}
            <span class="text-4xl">°F</span>
          </div>
          <div class="text-xs">
            feels like
            <span class="text-lg ml-2">{{ currentTemp.feels }}°F</span>
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
      <button class="border border-blue-200 rounded ml-4 mb-2" @click="isHidden = !isHidden">
        <p class="p-1 text-blue-200">Show Extended</p>
      </button>
      <div v-show="!isHidden" class="future-weather text-sm bg-blue-700 px-6 py-8 overflow-hidden">
        <div class="flex items-center" v-for="(day, index) in daily" :key="day.dt" :class="{ 'mt-8': index > 0 }">
          <div class="w-1/6 text-lg text-gray-200">
            {{ toWeekDay(day.dt) }}
          </div>
          <div class="w-4/6 px-4 flex flex-row items-center">
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
      isHidden: true
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
