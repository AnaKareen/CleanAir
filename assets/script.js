gsap.registerPlugin(ScrollTrigger);

//---------------------Landing Page ScrollTrigger---------------------
function LandingPageScrollTrigger() {
    gsap.to('body', { // LoadingAnimation
        opacity: 1, duration: 0,
    }); // /LoadingAnimation

    let landingPageTimeline = gsap.timeline({
        scrollTrigger: {
            trigger: ".landingPage",
            toggleActions: "restart restart restart restart",
            start: "0% 100%",
            end: "50% 0%",
            // markers: true, // Puedes activar los marcadores para depuración
        }
    });

    landingPageTimeline
        .from('.landingPage #logo', {
            opacity: 0, x: "-31%", duration: 2.2, ease: "sine.in",
        }, 0)
        .from('#wrapper #Navbar', {
            opacity: 0, x: "40%", duration: 2.2, ease: "sine.in",
        }, 0)
        .from('.landingPage nav span', {
            opacity: 0, x: "70%", duration: 2.2, stagger: 0.4, ease: "sine.inOut",
        }, 0.2)
        .from('.landingPage section h1 span', {
            opacity: 0, x: "-22%", duration: 2.2, ease: "sine.inOut",
        }, 0.4)
        .from('.landingPage #d', {
            opacity: 0, x: "22%", duration: 2.2, ease: "sine.inOut",
        }, 0.4)
        .from('.landingPage section p span', {
            opacity: 0, x: "-31%", duration: 2.2, stagger: 0.4, ease: "sine",
        }, 0.8)
        .from('.landingPage #ScrollDown p', {
            opacity: 0, y: "-61.8%", duration: 2.2, ease: "sine",
        }, 1.6)
        .from('.landingPage #dl', {
            opacity: 0, x: "22%", duration: 2.2, ease: "sine.inOut",
        }, 0.4);
}
//---------------------/Landing Page ScrollTrigger---------------------

//---------------------Slider ScrollTrigger---------------------
function SliderScrollTrigger() {
    const slides = ['.slide1', '.slide2', '.slide3'];

    slides.forEach(slide => {
        let slideTimeline = gsap.timeline({
            scrollTrigger: {
                trigger: slide,
                start: "100% 100%",
                end: "300% 0%",
                scrub: 2.2,
                pin: slide,
            }
        });

        slideTimeline
            .from(`${slide} #slide h1`, {
                opacity: 0, x: "-22%",
            })
            .from(`${slide} #slide p`, {
                opacity: 0, y: "22%",
            })
            .from(`${slide} #ImageContainer`, {
                opacity: 0, y: "22%",
            });
    });

    let iCodeAyush = gsap.timeline({
        scrollTrigger: {
            trigger: "#codeby",
            toggleActions: "restart restart restart restart",
            start: "48.618% 100%",
            end: "100% 0%",
            // markers: true, // Puedes activar los marcadores para depuración
        }
    });

    iCodeAyush.from('#codeby a', {
        opacity: 0, y: "130%", duration: 2.2, ease: "sine",
    });
}
//---------------------/Slider ScrollTrigger---------------------

//-------------Navbar (max-width: 400px)-------------
function Navbar() {
    gsap.from('#wrapper #Navbar', {
        opacity: 0, x: "40%", duration: 2.2, ease: "sine.in",
    });

    var navAni = gsap.timeline();
    navAni.from('#wrapper nav', {
        opacity: 0, y: "13%", duration: 0.5, ease: "sine",
    }).reverse();

    const navbar = document.querySelector("#wrapper #Navbar");
    if (navbar) {
        navbar.addEventListener("click", function () {
            const nav = document.querySelector("#wrapper nav");
            if (nav) {
                nav.classList.toggle("DisplayNone");
                navAni.reversed(!navAni.reversed ());
            }
        });
    } else {
        console.error('Navbar no encontrado');
    }
}
//-------------/Navbar (max-width: 400px)-------------

window.onload = () => {
    LandingPageScrollTrigger();
    SliderScrollTrigger();
    if (window.matchMedia("(max-width: 400px)").matches) {
        Navbar();
    }
}

// graficas
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM completamente cargado');
    
    const radios = document.querySelectorAll('input[type="radio"]');
    const valuesElements = document.querySelectorAll('.chart .values .value');

    // Verifica que se hayan encontrado los elementos de radio
    if (radios.length === 0) {
        console.error('No se encontraron radios en el DOM.');
        return; // Salir si no hay radios
    }

    // Verifica que se hayan encontrado los elementos de valores
    if (valuesElements.length === 0) {
        console.error('No se encontraron elementos de valores en el DOM.');
        return; // Salir si no hay elementos de valores
    }

    radios.forEach(radio => {
        radio.addEventListener('change', function() {
            const species = this.id; // Obtener la especie seleccionada
            fetchChartData(species); // Llamada para obtener los datos
        });
    });

    function fetchChartData(species) {
        // Petición AJAX para obtener los datos de la base de datos
        fetch(`/getChartData?species=${species}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => updateChart(data))
            .catch(error => console.error('Error fetching data:', error));
    }

    function updateChart(data) {
        const maxValue = 100; // Supongamos que el valor máximo es 100 para escalar
        data.forEach((value, index) => {
            const percentage = (value / maxValue) * 100;
            const xPosition = (index / (data.length - 1)) * 100;
            valuesElements[index].style.left = `${xPosition}%`;
            valuesElements[index].style.top = `${100 - percentage}%`;

            // Actualizar el tooltip
            valuesElements[index].setAttribute('data-value', `${value} billion`);
        });
    }

    // Inicializar el gráfico con la especie seleccionada por defecto
    const defaultSpecies = document.querySelector('input[name="species"]:checked');
    if (defaultSpecies) {
        fetchChartData(defaultSpecies.id);
    } else {
        console.error('No se encontró ninguna especie seleccionada por defecto.');
    }
});