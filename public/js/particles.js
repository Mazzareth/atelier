(function () {
    'use strict';

    var MAX_PARTICLES = 80;
    var CANVAS_ID = 'theme-particles';
    var THEMES = {
        default: { count: 50 },
        gay: { count: 60 },
        trans: { count: 45 },
        lesbian: { count: 50 },
        bi: { count: 40 },
        nonbinary: { count: 35 },
        pan: { count: 60 },
        asexual: { count: 40 },
        genderqueer: { count: 30 },
        intersex: { count: 20 },
        dickgirl: { count: 50 },
        femboy: { count: 50 },
        dominant: { count: 40 },
        submissive: { count: 35 },
        musk: { count: 30 },
        pup: { count: 45 },
        rubber: { count: 30 },
        rope: { count: 25 },
        inflation: { count: 15 },
        vore: { count: 35 },
        werewolf: { count: 40 },
        hypno: { count: 35 },
        daddy: { count: 40 },
        genderfluid: { count: 45 },
        guro: { count: 40 },
        living_toilet: { count: 30 },
        parasites: { count: 25 }
    };

    var system = {
        canvas: null,
        ctx: null,
        width: 0,
        height: 0,
        dpr: 1,
        theme: null,
        particles: [],
        rafId: null,
        observer: null,
        lastFrame: 0
    };

    var TWO_PI = Math.PI * 2;

    function clamp(value, min, max) {
        return Math.min(max, Math.max(min, value));
    }

    function rand(min, max) {
        return min + Math.random() * (max - min);
    }

    function pick(list) {
        return list[Math.floor(Math.random() * list.length)];
    }

    function readTheme() {
        return document.documentElement.getAttribute('data-theme') || 'default';
    }

    function readAnimSpeed() {
        return parseFloat(getComputedStyle(document.documentElement).getPropertyValue('--anim-speed').trim()) || 1;
    }

    function getCount(theme) {
        var themeConfig = THEMES[theme] || THEMES.default;
        return Math.min(themeConfig.count, MAX_PARTICLES);
    }

    function ensureCanvas() {
        var canvas = document.getElementById(CANVAS_ID);

        if (!canvas) {
            canvas = document.createElement('canvas');
            canvas.id = CANVAS_ID;
            canvas.setAttribute('aria-hidden', 'true');
            canvas.style.position = 'fixed';
            canvas.style.inset = '0';
            canvas.style.width = '100vw';
            canvas.style.height = '100vh';
            canvas.style.pointerEvents = 'none';
            canvas.style.zIndex = '-1';
            document.body.appendChild(canvas);
        }

        system.canvas = canvas;
        system.ctx = canvas.getContext('2d');
    }

    function resizeCanvas() {
        if (!system.canvas || !system.ctx) {
            return;
        }

        system.width = window.innerWidth;
        system.height = window.innerHeight;
        system.dpr = Math.max(1, window.devicePixelRatio || 1);
        system.canvas.width = Math.round(system.width * system.dpr);
        system.canvas.height = Math.round(system.height * system.dpr);
        system.ctx.setTransform(system.dpr, 0, 0, system.dpr, 0, 0);

        if (system.particles.length) {
            setTheme(system.theme || readTheme());
        }
    }

    function resetParticles() {
        system.particles = [];
        if (system.ctx) {
            system.ctx.clearRect(0, 0, system.width, system.height);
        }
    }

    function setTheme(theme) {
        system.theme = theme;
        resetParticles();

        var count = getCount(theme);
        for (var i = 0; i < count; i += 1) {
            system.particles.push(createParticle(theme, i));
        }
    }

    function createParticle(theme, index) {
        switch (theme) {
            case 'gay':
                return createGayParticle();
            case 'trans':
                return createTransParticle();
            case 'lesbian':
                return createLesbianParticle();
            case 'bi':
                return createBiParticle();
            case 'nonbinary':
                return createNonbinaryParticle();
            case 'pan':
                return createPanParticle();
            case 'asexual':
                return createAsexualParticle();
            case 'genderqueer':
                return createGenderqueerPair(index);
            case 'intersex':
                return createIntersexRing();
            case 'dickgirl':
                return createIconParticle(['heart', 'star'], ['#e62e8a'], 'dickgirl');
            case 'femboy':
                return createIconParticle(['heart', 'star'], ['#f5a9b8', '#5bcffa'], 'femboy');
            case 'dominant':
                return createDominantParticle();
            case 'submissive':
                return createSubmissiveParticle();
            case 'musk':
                return createMuskParticle();
            case 'pup':
                return createPupParticle();
            case 'rubber':
                return createRubberParticle();
            case 'rope':
                return createRopeParticle();
            case 'inflation':
                return createInflationParticle();
            case 'vore':
                return createVoreParticle();
            case 'werewolf':
                return createWerewolfParticle();
            case 'hypno':
                return index < 20 ? createHypnoRing(index) : createHypnoOrb();
            case 'daddy':
                return createDaddyParticle();
            case 'genderfluid':
                return createGenderfluidParticle();
            case 'guro':
                return createGuroParticle();
            case 'living_toilet':
                return createLivingToiletParticle();
            case 'parasites':
                return createParasiteParticle();
            case 'default':
            default:
                return createDefaultParticle();
        }
    }

    function createDefaultParticle() {
        return {
            type: 'default',
            x: rand(0, system.width),
            y: rand(0, system.height),
            radius: rand(1.5, 4.2),
            speedY: rand(0.12, 0.42),
            drift: rand(-0.15, 0.15),
            alpha: rand(0.18, 0.42),
            color: '#2bdc6c'
        };
    }

    function createGayParticle() {
        return {
            type: 'gay',
            x: rand(0, system.width),
            y: rand(system.height * 0.72, system.height + 30),
            radius: rand(1, 2.4),
            speedY: rand(0.5, 1.2),
            sway: rand(0.3, 1),
            swayPhase: rand(0, TWO_PI),
            hueShift: rand(0, 5.99),
            colorIndex: Math.floor(rand(0, 6))
        };
    }

    function createTransParticle() {
        return {
            type: 'trans',
            x: rand(0, system.width),
            y: rand(0, system.height),
            radius: rand(3, 8),
            baseX: rand(0, system.width),
            phase: rand(0, TWO_PI),
            driftY: rand(-0.22, 0.22),
            driftX: rand(10, 34),
            speed: rand(0.004, 0.011),
            color: pick(['#55cdfc', '#f7a8b8', '#ffffff']),
            alpha: rand(0.18, 0.4)
        };
    }

    function createLesbianParticle() {
        return {
            type: 'lesbian',
            x: rand(0, system.width),
            y: rand(system.height * 0.25, system.height + 20),
            radius: rand(1.8, 4.6),
            speedY: rand(0.24, 0.68),
            speedX: rand(-0.3, 0.3),
            alpha: rand(0.2, 0.42),
            color: '#ff8f50'
        };
    }

    function createBiParticle() {
        return {
            type: 'bi',
            x: rand(0, system.width),
            y: rand(0, system.height),
            size: rand(6, 14),
            angle: rand(0, TWO_PI),
            rotation: rand(-0.02, 0.02),
            speedX: rand(-0.25, 0.25),
            speedY: rand(-0.12, 0.22),
            color: pick(['#d60270', '#9b4f96', '#0038a8']),
            alpha: rand(0.32, 0.58)
        };
    }

    function createNonbinaryParticle() {
        return {
            type: 'nonbinary',
            x: rand(0, system.width),
            y: rand(0, system.height),
            size: rand(4, 10),
            speedX: rand(-0.55, 0.55),
            speedY: rand(-0.45, 0.45),
            shape: Math.random() > 0.45 ? 'square' : 'dot',
            color: Math.random() > 0.5 ? '#fcf434' : '#9c59d1',
            alpha: rand(0.28, 0.56)
        };
    }

    function createPanParticle() {
        return {
            type: 'pan',
            x: rand(0, system.width),
            y: rand(-system.height, -10),
            size: rand(6, 14),
            speedY: rand(0.8, 1.8),
            drift: rand(-0.45, 0.45),
            angle: rand(0, TWO_PI),
            spin: rand(-0.03, 0.03),
            color: pick(['#ff218c', '#ffd800', '#21b1ff']),
            alpha: rand(0.38, 0.68)
        };
    }

    function createAsexualParticle() {
        return {
            type: 'asexual',
            x: rand(0, system.width),
            y: rand(-40, system.height),
            radius: rand(2, 5.2),
            speedY: rand(0.12, 0.42),
            speedX: rand(-0.12, 0.12),
            colorTop: '#6d6476',
            colorBottom: '#9b5de5',
            alpha: rand(0.16, 0.34)
        };
    }

    function createGenderqueerPair(index) {
        return {
            type: 'genderqueer',
            cx: rand(0, system.width),
            cy: rand(0, system.height),
            orbitRadius: rand(6, 18),
            pairRadius: rand(3, 6),
            angle: rand(0, TWO_PI),
            speed: rand(0.01, 0.026),
            driftX: rand(-0.18, 0.18),
            driftY: rand(-0.18, 0.18),
            colors: index % 2 === 0 ? ['#b57edc', '#6bc56b'] : ['#6bc56b', '#b57edc'],
            alpha: rand(0.24, 0.48)
        };
    }

    function createIntersexRing() {
        return {
            type: 'intersex',
            x: system.width / 2 + rand(-system.width * 0.08, system.width * 0.08),
            y: system.height / 2 + rand(-system.height * 0.08, system.height * 0.08),
            radius: rand(8, 28),
            growth: rand(0.4, 1),
            lineWidth: rand(1.2, 3.4),
            alpha: rand(0.24, 0.56),
            color: '#ffd800'
        };
    }

    function createIconParticle(shapes, colors, theme) {
        return {
            type: theme,
            x: rand(0, system.width),
            y: rand(system.height * 0.35, system.height + 20),
            size: rand(7, 14),
            speedY: rand(0.18, 0.62),
            speedX: rand(-0.24, 0.24),
            bounce: theme === 'femboy' ? rand(0.2, 0.45) : 0,
            shape: pick(shapes),
            color: pick(colors),
            alpha: rand(0.28, 0.62),
            phase: rand(0, TWO_PI)
        };
    }

    function createDominantParticle() {
        return {
            type: 'dominant',
            x: rand(0, system.width),
            y: rand(-system.height, -15),
            width: rand(5, 12),
            height: rand(14, 34),
            angle: rand(-0.7, 0.7),
            speedY: rand(1.1, 2.6),
            speedX: rand(-0.22, 0.22),
            color: Math.random() > 0.45 ? '#cc0000' : '#101010',
            alpha: rand(0.34, 0.68)
        };
    }

    function createSubmissiveParticle() {
        return {
            type: 'submissive',
            x: rand(0, system.width),
            y: rand(-30, system.height),
            radius: rand(1.4, 3.8),
            speedY: rand(0.08, 0.24),
            speedX: rand(-0.08, 0.08),
            twinkle: rand(0.004, 0.012),
            phase: rand(0, TWO_PI),
            color: '#9d8ec7'
        };
    }

    function createMuskParticle() {
        return {
            type: 'musk',
            x: rand(0, system.width),
            y: rand(system.height * 0.4, system.height + 20),
            radius: rand(14, 28),
            speedY: rand(0.18, 0.42),
            waveAmp: rand(6, 16),
            waveFreq: rand(0.008, 0.022),
            phase: rand(0, TWO_PI),
            alpha: rand(0.08, 0.18),
            color: 'rgba(181, 141, 61, 0.16)'
        };
    }

    function createPupParticle() {
        return {
            type: 'pup',
            x: rand(0, system.width),
            y: rand(0, system.height),
            radius: rand(2.5, 5.8),
            speedX: rand(-0.9, 0.9),
            speedY: rand(-0.9, 0.9),
            jitter: rand(0.08, 0.22),
            alpha: rand(0.36, 0.68),
            color: '#2b5cff'
        };
    }

    function createRubberParticle() {
        return {
            type: 'rubber',
            x: rand(0, system.width),
            y: rand(0, system.height),
            width: rand(6, 18),
            height: rand(12, 28),
            angle: rand(0, TWO_PI),
            speedX: rand(-0.22, 0.22),
            speedY: rand(-0.16, 0.16),
            color: Math.random() > 0.5 ? '#111111' : '#f7f7f7',
            flash: Math.random() > 0.88,
            alpha: rand(0.22, 0.58)
        };
    }

    function createRopeParticle() {
        return {
            type: 'rope',
            x: rand(-system.width * 0.1, system.width),
            y: rand(-system.height * 0.1, system.height),
            length: rand(14, 32),
            angle: rand(0.45, 0.85),
            speedX: rand(0.08, 0.22),
            speedY: rand(0.05, 0.16),
            alpha: rand(0.26, 0.54),
            color: '#c8a56e'
        };
    }

    function createInflationParticle() {
        return {
            type: 'inflation',
            x: rand(0, system.width),
            y: rand(0, system.height),
            radius: rand(22, 54),
            speedX: rand(-0.18, 0.18),
            speedY: rand(-0.15, 0.15),
            pulse: rand(0, TWO_PI),
            pulseSpeed: rand(0.008, 0.018),
            alpha: rand(0.14, 0.24),
            color: pick([
                'rgba(255, 161, 207, 0.18)',
                'rgba(126, 183, 255, 0.18)',
                'rgba(255, 228, 117, 0.18)'
            ])
        };
    }

    function createVoreParticle() {
        return {
            type: 'vore',
            x: rand(0, system.width),
            y: rand(-system.height, -20),
            length: rand(18, 42),
            width: rand(5, 10),
            speedY: rand(0.35, 0.9),
            wobble: rand(0, TWO_PI),
            color: '#6bb86f',
            alpha: rand(0.28, 0.48)
        };
    }

    function createWerewolfParticle() {
        return {
            type: 'werewolf',
            x: rand(0, system.width),
            y: rand(0, system.height),
            length: rand(12, 34),
            angle: rand(-1.15, -0.55),
            speedX: rand(0.35, 0.75),
            speedY: rand(0.18, 0.42),
            alpha: rand(0.18, 0.38),
            color: '#c6c8d2'
        };
    }

    function createHypnoRing(index) {
        return {
            type: 'hypno-ring',
            radius: (index / 20) * Math.max(system.width, system.height) * 0.7,
            growth: rand(0.8, 1.5),
            alpha: rand(0.12, 0.28),
            lineWidth: rand(1.4, 3.2),
            offsetX: rand(-20, 20),
            offsetY: rand(-20, 20)
        };
    }

    function createHypnoOrb() {
        return {
            type: 'hypno-orb',
            orbitRadius: rand(45, Math.min(system.width, system.height) * 0.34),
            angle: rand(0, TWO_PI),
            speed: rand(0.004, 0.012),
            size: rand(4, 10),
            color: '#8a7dff',
            alpha: rand(0.26, 0.52)
        };
    }

    function createDaddyParticle() {
        return {
            type: 'daddy',
            x: rand(0, system.width),
            y: rand(system.height * 0.3, system.height + 20),
            radius: rand(1.8, 4.8),
            speedY: rand(0.18, 0.52),
            speedX: rand(-0.12, 0.12),
            alpha: rand(0.22, 0.5),
            color: '#d59b45'
        };
    }

    function createGenderfluidParticle() {
        return {
            type: 'genderfluid',
            x: rand(0, system.width),
            y: rand(0, system.height),
            radius: rand(2.4, 5.4),
            speedX: rand(-0.45, 0.45),
            speedY: rand(-0.45, 0.45),
            colorIndex: Math.floor(rand(0, 5)),
            tail: []
        };
    }

    function createGuroParticle() {
        return {
            type: 'guro',
            x: rand(0, system.width),
            y: rand(-system.height, -10),
            width: rand(6, 16),
            height: rand(5, 12),
            speedY: rand(0.28, 0.9),
            drift: rand(-0.18, 0.18),
            angle: rand(0, TWO_PI),
            spin: rand(-0.02, 0.02),
            color: Math.random() > 0.88 ? '#8d0f22' : '#f8f4ef',
            alpha: rand(0.24, 0.46)
        };
    }

    function createLivingToiletParticle() {
        return {
            type: 'living_toilet',
            x: rand(0, system.width),
            y: rand(-system.height, -20),
            radius: rand(6, 14),
            speedY: rand(0.22, 0.62),
            stretch: rand(1.2, 2),
            alpha: rand(0.14, 0.3),
            color: '#b49343'
        };
    }

    function createParasiteParticle() {
        return {
            type: 'parasites',
            x: rand(-40, system.width + 40),
            y: rand(0, system.height),
            length: rand(18, 40),
            speedX: rand(0.3, 0.8) * (Math.random() > 0.5 ? 1 : -1),
            waveAmp: rand(4, 12),
            waveFreq: rand(0.02, 0.05),
            phase: rand(0, TWO_PI),
            trail: [],
            color: Math.random() > 0.5 ? '#86c453' : '#1e3218',
            alpha: rand(0.28, 0.52)
        };
    }

    function updateParticle(particle, speed, time) {
        switch (particle.type) {
            case 'default':
                particle.y -= particle.speedY * speed;
                particle.x += particle.drift * speed;
                if (particle.y < -10) {
                    Object.assign(particle, createDefaultParticle(), { y: system.height + rand(0, 30) });
                }
                break;
            case 'gay':
                particle.y -= particle.speedY * speed;
                particle.x += Math.sin(time * 0.01 + particle.swayPhase) * particle.sway * 0.12 * speed;
                particle.hueShift += 0.04 * speed;
                if (particle.y < -8) {
                    Object.assign(particle, createGayParticle());
                }
                break;
            case 'trans':
                particle.phase += particle.speed * speed * 10;
                particle.y += particle.driftY * speed;
                particle.x += Math.sin(particle.phase) * 0.35 * speed;
                if (particle.y < -20) {
                    particle.y = system.height + 20;
                } else if (particle.y > system.height + 20) {
                    particle.y = -20;
                }
                if (particle.x < -20) {
                    particle.x = system.width + 20;
                } else if (particle.x > system.width + 20) {
                    particle.x = -20;
                }
                break;
            case 'lesbian':
                particle.y -= particle.speedY * speed;
                particle.x += particle.speedX * speed;
                if (particle.y < -10) {
                    Object.assign(particle, createLesbianParticle());
                }
                break;
            case 'bi':
                particle.x += particle.speedX * speed;
                particle.y += particle.speedY * speed;
                particle.angle += particle.rotation * speed * 6;
                wrapParticle(particle, 20);
                break;
            case 'nonbinary':
                particle.x += particle.speedX * speed;
                particle.y += particle.speedY * speed;
                if (particle.x <= 0 || particle.x >= system.width) {
                    particle.speedX *= -1;
                }
                if (particle.y <= 0 || particle.y >= system.height) {
                    particle.speedY *= -1;
                }
                break;
            case 'pan':
                particle.y += particle.speedY * speed;
                particle.x += particle.drift * speed;
                particle.angle += particle.spin * speed * 8;
                if (particle.y > system.height + 20) {
                    Object.assign(particle, createPanParticle());
                }
                break;
            case 'asexual':
                particle.y += particle.speedY * speed;
                particle.x += particle.speedX * speed;
                if (particle.y > system.height + 10) {
                    Object.assign(particle, createAsexualParticle(), { y: -10 });
                }
                break;
            case 'genderqueer':
                particle.cx += particle.driftX * speed;
                particle.cy += particle.driftY * speed;
                particle.angle += particle.speed * speed * 8;
                wrapPair(particle, 28);
                break;
            case 'intersex':
                particle.radius += particle.growth * speed;
                particle.alpha -= 0.004 * speed;
                if (particle.alpha <= 0) {
                    Object.assign(particle, createIntersexRing());
                }
                break;
            case 'dickgirl':
            case 'femboy':
                particle.y -= particle.speedY * speed;
                particle.x += particle.speedX * speed + Math.sin(time * 0.01 + particle.phase) * 0.2 * speed;
                if (particle.type === 'femboy') {
                    particle.y += Math.sin(time * 0.008 + particle.phase) * particle.bounce * speed;
                }
                if (particle.y < -20) {
                    Object.assign(particle, createIconParticle(['heart', 'star'], particle.type === 'femboy' ? ['#f5a9b8', '#5bcffa'] : ['#e62e8a'], particle.type));
                }
                break;
            case 'dominant':
                particle.y += particle.speedY * speed;
                particle.x += particle.speedX * speed;
                if (particle.y > system.height + 40) {
                    Object.assign(particle, createDominantParticle());
                }
                break;
            case 'submissive':
                particle.phase += particle.twinkle * speed * 20;
                particle.y += particle.speedY * speed;
                particle.x += particle.speedX * speed;
                if (particle.y > system.height + 10) {
                    Object.assign(particle, createSubmissiveParticle(), { y: -6 });
                }
                break;
            case 'musk':
                particle.y -= particle.speedY * speed;
                particle.phase += particle.waveFreq * speed * 20;
                particle.x += Math.sin(particle.phase) * 0.45 * speed;
                if (particle.y < -40) {
                    Object.assign(particle, createMuskParticle());
                }
                break;
            case 'pup':
                particle.x += particle.speedX * speed + rand(-particle.jitter, particle.jitter);
                particle.y += particle.speedY * speed + rand(-particle.jitter, particle.jitter);
                if (particle.x <= particle.radius || particle.x >= system.width - particle.radius) {
                    particle.speedX *= -1;
                }
                if (particle.y <= particle.radius || particle.y >= system.height - particle.radius) {
                    particle.speedY *= -1;
                }
                break;
            case 'rubber':
                particle.x += particle.speedX * speed;
                particle.y += particle.speedY * speed;
                particle.flash = Math.random() > 0.992;
                particle.angle += 0.01 * speed;
                wrapParticle(particle, 24);
                break;
            case 'rope':
                particle.x += particle.speedX * speed;
                particle.y += particle.speedY * speed;
                if (particle.x > system.width + particle.length || particle.y > system.height + particle.length) {
                    Object.assign(particle, createRopeParticle(), {
                        x: rand(-system.width * 0.15, 0),
                        y: rand(-system.height * 0.15, system.height * 0.5)
                    });
                }
                break;
            case 'inflation':
                particle.x += particle.speedX * speed;
                particle.y += particle.speedY * speed;
                particle.pulse += particle.pulseSpeed * speed * 8;
                wrapParticle(particle, particle.radius);
                break;
            case 'vore':
                particle.y += particle.speedY * speed;
                particle.x += Math.sin(time * 0.006 + particle.wobble) * 0.16 * speed;
                if (particle.y > system.height + particle.length) {
                    Object.assign(particle, createVoreParticle());
                }
                break;
            case 'werewolf':
                particle.x += particle.speedX * speed;
                particle.y += particle.speedY * speed;
                if (particle.x > system.width + particle.length || particle.y > system.height + particle.length) {
                    Object.assign(particle, createWerewolfParticle(), {
                        x: rand(-40, system.width * 0.3),
                        y: rand(-40, system.height)
                    });
                }
                break;
            case 'hypno-ring':
                particle.radius += particle.growth * speed;
                particle.alpha -= 0.0025 * speed;
                if (particle.alpha <= 0 || particle.radius > Math.max(system.width, system.height) * 0.8) {
                    Object.assign(particle, createHypnoRing(Math.floor(rand(0, 19))));
                    particle.radius = 0;
                }
                break;
            case 'hypno-orb':
                particle.angle += particle.speed * speed * 10;
                break;
            case 'daddy':
                particle.y -= particle.speedY * speed;
                particle.x += particle.speedX * speed;
                if (particle.y < -10) {
                    Object.assign(particle, createDaddyParticle());
                }
                break;
            case 'genderfluid':
                particle.tail.unshift({ x: particle.x, y: particle.y });
                particle.tail = particle.tail.slice(0, 6);
                particle.x += particle.speedX * speed;
                particle.y += particle.speedY * speed;
                if (particle.x <= 0 || particle.x >= system.width) {
                    particle.speedX *= -1;
                    particle.colorIndex = (particle.colorIndex + 1) % 5;
                }
                if (particle.y <= 0 || particle.y >= system.height) {
                    particle.speedY *= -1;
                    particle.colorIndex = (particle.colorIndex + 1) % 5;
                }
                break;
            case 'guro':
                particle.y += particle.speedY * speed;
                particle.x += particle.drift * speed;
                particle.angle += particle.spin * speed * 8;
                if (particle.y > system.height + 20) {
                    Object.assign(particle, createGuroParticle());
                }
                break;
            case 'living_toilet':
                particle.y += particle.speedY * speed;
                if (particle.y > system.height + particle.radius * particle.stretch) {
                    Object.assign(particle, createLivingToiletParticle());
                }
                break;
            case 'parasites':
                particle.trail.unshift({ x: particle.x, y: particle.y });
                particle.trail = particle.trail.slice(0, 8);
                particle.phase += particle.waveFreq * speed * 18;
                particle.x += particle.speedX * speed;
                particle.y += Math.sin(particle.phase) * 0.6 * speed;
                if (particle.x < -60 || particle.x > system.width + 60) {
                    Object.assign(particle, createParasiteParticle(), {
                        x: particle.speedX > 0 ? -40 : system.width + 40
                    });
                }
                break;
        }
    }

    function wrapParticle(particle, margin) {
        if (particle.x < -margin) {
            particle.x = system.width + margin;
        } else if (particle.x > system.width + margin) {
            particle.x = -margin;
        }

        if (particle.y < -margin) {
            particle.y = system.height + margin;
        } else if (particle.y > system.height + margin) {
            particle.y = -margin;
        }
    }

    function wrapPair(particle, margin) {
        if (particle.cx < -margin) {
            particle.cx = system.width + margin;
        } else if (particle.cx > system.width + margin) {
            particle.cx = -margin;
        }

        if (particle.cy < -margin) {
            particle.cy = system.height + margin;
        } else if (particle.cy > system.height + margin) {
            particle.cy = -margin;
        }
    }

    function renderParticle(ctx, particle, time) {
        switch (particle.type) {
            case 'default':
                drawCircle(ctx, particle.x, particle.y, particle.radius, particle.color, particle.alpha * clamp(1 - particle.y / Math.max(system.height, 1), 0, 1));
                break;
            case 'gay':
                drawSparkle(ctx, particle.x, particle.y, particle.radius * 2.5, gayColor(particle), 0.72);
                break;
            case 'trans':
                drawCircle(ctx, particle.x + Math.sin(particle.phase) * particle.driftX, particle.y, particle.radius, particle.color, particle.alpha);
                break;
            case 'lesbian':
                drawCircle(ctx, particle.x, particle.y, particle.radius, particle.color, particle.alpha);
                break;
            case 'bi':
                drawDiamond(ctx, particle.x, particle.y, particle.size, particle.angle, particle.color, particle.alpha);
                break;
            case 'nonbinary':
                if (particle.shape === 'square') {
                    drawSquare(ctx, particle.x, particle.y, particle.size, particle.color, particle.alpha);
                } else {
                    drawCircle(ctx, particle.x, particle.y, particle.size * 0.45, particle.color, particle.alpha);
                }
                break;
            case 'pan':
                drawTriangle(ctx, particle.x, particle.y, particle.size, particle.angle, particle.color, particle.alpha);
                break;
            case 'asexual':
                drawGradientCircle(ctx, particle.x, particle.y, particle.radius, particle.colorTop, particle.colorBottom, particle.alpha);
                break;
            case 'genderqueer':
                drawOrbitPair(ctx, particle);
                break;
            case 'intersex':
                drawRing(ctx, particle.x, particle.y, particle.radius, particle.lineWidth, particle.color, particle.alpha);
                break;
            case 'dickgirl':
            case 'femboy':
                if (particle.shape === 'heart') {
                    drawHeart(ctx, particle.x, particle.y, particle.size, particle.color, particle.alpha);
                } else {
                    drawStar(ctx, particle.x, particle.y, particle.size * 0.55, particle.color, particle.alpha);
                }
                break;
            case 'dominant':
                drawShard(ctx, particle);
                break;
            case 'submissive':
                drawSparkle(ctx, particle.x, particle.y, particle.radius * 2.4, particle.color, 0.24 + (Math.sin(particle.phase) + 1) * 0.15);
                break;
            case 'musk':
                drawBlob(ctx, particle.x, particle.y, particle.radius, particle.color, particle.alpha);
                break;
            case 'pup':
                drawCircle(ctx, particle.x, particle.y, particle.radius, particle.color, particle.alpha);
                break;
            case 'rubber':
                drawRubberRect(ctx, particle);
                break;
            case 'rope':
                drawRopeLine(ctx, particle);
                break;
            case 'inflation':
                drawInflationOrb(ctx, particle);
                break;
            case 'vore':
                drawDrip(ctx, particle.x, particle.y, particle.width, particle.length, particle.color, particle.alpha);
                break;
            case 'werewolf':
                drawFurStroke(ctx, particle);
                break;
            case 'hypno-ring':
                drawRing(
                    ctx,
                    system.width / 2 + particle.offsetX,
                    system.height / 2 + particle.offsetY,
                    particle.radius,
                    particle.lineWidth,
                    '#9b6dff',
                    particle.alpha
                );
                break;
            case 'hypno-orb':
                drawHypnoOrb(ctx, particle, time);
                break;
            case 'daddy':
                drawCircle(ctx, particle.x, particle.y, particle.radius, particle.color, particle.alpha);
                break;
            case 'genderfluid':
                drawGenderfluidTrail(ctx, particle);
                break;
            case 'guro':
                drawFragment(ctx, particle);
                break;
            case 'living_toilet':
                drawDrop(ctx, particle.x, particle.y, particle.radius, particle.stretch, particle.color, particle.alpha);
                break;
            case 'parasites':
                drawParasite(ctx, particle);
                break;
        }
    }

    function gayColor(particle) {
        var colors = ['#ff0018', '#ffa52c', '#ffff41', '#008018', '#0000f9', '#86007d'];
        return colors[(particle.colorIndex + Math.floor(particle.hueShift)) % colors.length];
    }

    function drawCircle(ctx, x, y, radius, color, alpha) {
        ctx.save();
        ctx.globalAlpha = alpha;
        ctx.fillStyle = color;
        ctx.beginPath();
        ctx.arc(x, y, radius, 0, TWO_PI);
        ctx.fill();
        ctx.restore();
    }

    function drawGradientCircle(ctx, x, y, radius, colorTop, colorBottom, alpha) {
        ctx.save();
        ctx.globalAlpha = alpha;
        var gradient = ctx.createLinearGradient(x, y - radius, x, y + radius);
        gradient.addColorStop(0, colorTop);
        gradient.addColorStop(1, colorBottom);
        ctx.fillStyle = gradient;
        ctx.beginPath();
        ctx.arc(x, y, radius, 0, TWO_PI);
        ctx.fill();
        ctx.restore();
    }

    function drawSquare(ctx, x, y, size, color, alpha) {
        ctx.save();
        ctx.globalAlpha = alpha;
        ctx.fillStyle = color;
        ctx.fillRect(x - size / 2, y - size / 2, size, size);
        ctx.restore();
    }

    function drawDiamond(ctx, x, y, size, angle, color, alpha) {
        ctx.save();
        ctx.translate(x, y);
        ctx.rotate(angle);
        ctx.globalAlpha = alpha;
        ctx.fillStyle = color;
        ctx.beginPath();
        ctx.moveTo(0, -size);
        ctx.lineTo(size * 0.75, 0);
        ctx.lineTo(0, size);
        ctx.lineTo(-size * 0.75, 0);
        ctx.closePath();
        ctx.fill();
        ctx.restore();
    }

    function drawTriangle(ctx, x, y, size, angle, color, alpha) {
        ctx.save();
        ctx.translate(x, y);
        ctx.rotate(angle);
        ctx.globalAlpha = alpha;
        ctx.fillStyle = color;
        ctx.beginPath();
        ctx.moveTo(0, -size);
        ctx.lineTo(size * 0.86, size * 0.6);
        ctx.lineTo(-size * 0.86, size * 0.6);
        ctx.closePath();
        ctx.fill();
        ctx.restore();
    }

    function drawSparkle(ctx, x, y, size, color, alpha) {
        ctx.save();
        ctx.translate(x, y);
        ctx.globalAlpha = alpha;
        ctx.strokeStyle = color;
        ctx.lineWidth = 1.2;
        ctx.beginPath();
        ctx.moveTo(-size, 0);
        ctx.lineTo(size, 0);
        ctx.moveTo(0, -size);
        ctx.lineTo(0, size);
        ctx.stroke();
        ctx.restore();
    }

    function drawOrbitPair(ctx, particle) {
        var dx = Math.cos(particle.angle) * particle.orbitRadius;
        var dy = Math.sin(particle.angle) * particle.orbitRadius;
        drawCircle(ctx, particle.cx + dx, particle.cy + dy, particle.pairRadius, particle.colors[0], particle.alpha);
        drawCircle(ctx, particle.cx - dx, particle.cy - dy, particle.pairRadius, particle.colors[1], particle.alpha);
        ctx.save();
        ctx.globalAlpha = particle.alpha * 0.5;
        ctx.strokeStyle = particle.colors[0];
        ctx.lineWidth = 1;
        ctx.beginPath();
        ctx.moveTo(particle.cx + dx, particle.cy + dy);
        ctx.lineTo(particle.cx - dx, particle.cy - dy);
        ctx.stroke();
        ctx.restore();
    }

    function drawRing(ctx, x, y, radius, lineWidth, color, alpha) {
        ctx.save();
        ctx.globalAlpha = alpha;
        ctx.strokeStyle = color;
        ctx.lineWidth = lineWidth;
        ctx.shadowColor = color;
        ctx.shadowBlur = 12;
        ctx.beginPath();
        ctx.arc(x, y, radius, 0, TWO_PI);
        ctx.stroke();
        ctx.restore();
    }

    function drawHeart(ctx, x, y, size, color, alpha) {
        ctx.save();
        ctx.translate(x, y);
        ctx.scale(size / 12, size / 12);
        ctx.globalAlpha = alpha;
        ctx.fillStyle = color;
        ctx.beginPath();
        ctx.moveTo(0, 4);
        ctx.bezierCurveTo(0, -2, -10, -2, -10, 5);
        ctx.bezierCurveTo(-10, 12, 0, 15, 0, 20);
        ctx.bezierCurveTo(0, 15, 10, 12, 10, 5);
        ctx.bezierCurveTo(10, -2, 0, -2, 0, 4);
        ctx.fill();
        ctx.restore();
    }

    function drawStar(ctx, x, y, radius, color, alpha) {
        var spikes = 5;
        var outer = radius;
        var inner = radius * 0.45;
        var rot = Math.PI / 2 * 3;
        var step = Math.PI / spikes;

        ctx.save();
        ctx.translate(x, y);
        ctx.globalAlpha = alpha;
        ctx.fillStyle = color;
        ctx.beginPath();
        ctx.moveTo(0, -outer);
        for (var i = 0; i < spikes; i += 1) {
            ctx.lineTo(Math.cos(rot) * outer, Math.sin(rot) * outer);
            rot += step;
            ctx.lineTo(Math.cos(rot) * inner, Math.sin(rot) * inner);
            rot += step;
        }
        ctx.closePath();
        ctx.fill();
        ctx.restore();
    }

    function drawShard(ctx, particle) {
        ctx.save();
        ctx.translate(particle.x, particle.y);
        ctx.rotate(particle.angle);
        ctx.globalAlpha = particle.alpha;
        ctx.fillStyle = particle.color;
        ctx.beginPath();
        ctx.moveTo(0, -particle.height / 2);
        ctx.lineTo(particle.width / 2, -particle.height * 0.1);
        ctx.lineTo(particle.width * 0.2, particle.height / 2);
        ctx.lineTo(-particle.width / 2, particle.height * 0.3);
        ctx.closePath();
        ctx.fill();
        ctx.restore();
    }

    function drawBlob(ctx, x, y, radius, color, alpha) {
        ctx.save();
        ctx.globalAlpha = alpha;
        ctx.fillStyle = color;
        ctx.beginPath();
        ctx.ellipse(x, y, radius, radius * 0.55, Math.sin(y * 0.01) * 0.5, 0, TWO_PI);
        ctx.fill();
        ctx.restore();
    }

    function drawRubberRect(ctx, particle) {
        ctx.save();
        ctx.translate(particle.x, particle.y);
        ctx.rotate(particle.angle);
        ctx.globalAlpha = particle.alpha;
        ctx.fillStyle = particle.color;
        ctx.fillRect(-particle.width / 2, -particle.height / 2, particle.width, particle.height);
        if (particle.flash) {
            ctx.fillStyle = 'rgba(255,255,255,0.9)';
            ctx.fillRect(-particle.width / 2, -particle.height / 2, particle.width, 2);
        }
        ctx.restore();
    }

    function drawRopeLine(ctx, particle) {
        ctx.save();
        ctx.globalAlpha = particle.alpha;
        ctx.strokeStyle = particle.color;
        ctx.lineWidth = 1.2;
        ctx.beginPath();
        ctx.moveTo(particle.x, particle.y);
        ctx.lineTo(
            particle.x + Math.cos(particle.angle) * particle.length,
            particle.y + Math.sin(particle.angle) * particle.length
        );
        ctx.stroke();
        ctx.restore();
    }

    function drawInflationOrb(ctx, particle) {
        var scale = 1 + Math.sin(particle.pulse) * 0.08;
        ctx.save();
        ctx.globalAlpha = particle.alpha;
        ctx.fillStyle = particle.color;
        ctx.beginPath();
        ctx.arc(particle.x, particle.y, particle.radius * scale, 0, TWO_PI);
        ctx.fill();
        ctx.restore();
    }

    function drawDrip(ctx, x, y, width, length, color, alpha) {
        ctx.save();
        ctx.globalAlpha = alpha;
        ctx.fillStyle = color;
        ctx.beginPath();
        ctx.moveTo(x - width / 2, y - length / 2);
        ctx.quadraticCurveTo(x + width / 2, y, x, y + length / 2);
        ctx.quadraticCurveTo(x - width, y, x - width / 2, y - length / 2);
        ctx.fill();
        ctx.restore();
    }

    function drawFurStroke(ctx, particle) {
        ctx.save();
        ctx.globalAlpha = particle.alpha;
        ctx.strokeStyle = particle.color;
        ctx.lineWidth = 1.1;
        ctx.beginPath();
        ctx.moveTo(particle.x, particle.y);
        ctx.lineTo(
            particle.x + Math.cos(particle.angle) * particle.length,
            particle.y + Math.sin(particle.angle) * particle.length
        );
        ctx.stroke();
        ctx.restore();
    }

    function drawHypnoOrb(ctx, particle, time) {
        var centerX = system.width / 2;
        var centerY = system.height / 2;
        var x = centerX + Math.cos(particle.angle + time * 0.0002) * particle.orbitRadius;
        var y = centerY + Math.sin(particle.angle + time * 0.0002) * particle.orbitRadius;
        drawCircle(ctx, x, y, particle.size, particle.color, particle.alpha);
    }

    function drawGenderfluidTrail(ctx, particle) {
        var colors = ['#ff76c8', '#ffffff', '#b57edc', '#2f3cbe', '#1eb6ff'];
        for (var i = particle.tail.length - 1; i >= 0; i -= 1) {
            var point = particle.tail[i];
            drawCircle(ctx, point.x, point.y, particle.radius * (i + 1) / (particle.tail.length + 1), colors[(particle.colorIndex + i) % colors.length], 0.08 + (i / particle.tail.length) * 0.22);
        }
        drawCircle(ctx, particle.x, particle.y, particle.radius, colors[particle.colorIndex], 0.64);
    }

    function drawFragment(ctx, particle) {
        ctx.save();
        ctx.translate(particle.x, particle.y);
        ctx.rotate(particle.angle);
        ctx.globalAlpha = particle.alpha;
        ctx.fillStyle = particle.color;
        ctx.fillRect(-particle.width / 2, -particle.height / 2, particle.width, particle.height);
        ctx.restore();
    }

    function drawDrop(ctx, x, y, radius, stretch, color, alpha) {
        ctx.save();
        ctx.globalAlpha = alpha;
        ctx.fillStyle = color;
        ctx.beginPath();
        ctx.ellipse(x, y, radius * 0.7, radius * stretch, 0, 0, TWO_PI);
        ctx.fill();
        ctx.restore();
    }

    function drawParasite(ctx, particle) {
        ctx.save();
        ctx.globalAlpha = particle.alpha;
        ctx.strokeStyle = particle.color;
        ctx.lineWidth = 2;
        ctx.beginPath();
        ctx.moveTo(particle.x, particle.y);
        for (var i = 0; i < particle.length; i += 1) {
            ctx.lineTo(
                particle.x - i * Math.sign(particle.speedX),
                particle.y + Math.sin(particle.phase - i * 0.4) * particle.waveAmp * 0.3
            );
        }
        ctx.stroke();
        ctx.restore();

        for (var j = 0; j < particle.trail.length; j += 1) {
            var trailPoint = particle.trail[j];
            drawCircle(ctx, trailPoint.x, trailPoint.y, 1.2, particle.color, 0.08 + (particle.trail.length - j) * 0.02);
        }
    }

    function frame(now) {
        if (!system.ctx) {
            return;
        }

        var delta = system.lastFrame ? now - system.lastFrame : 16.67;
        system.lastFrame = now;
        var speed = clamp((delta / 16.67) * readAnimSpeed(), 0.2, 2.5);

        system.ctx.clearRect(0, 0, system.width, system.height);

        for (var i = 0; i < system.particles.length; i += 1) {
            var particle = system.particles[i];
            updateParticle(particle, speed, now);
            renderParticle(system.ctx, particle, now);
        }

        system.rafId = window.requestAnimationFrame(frame);
    }

    function bindObservers() {
        if (system.observer) {
            system.observer.disconnect();
        }

        system.observer = new MutationObserver(function (mutations) {
            for (var i = 0; i < mutations.length; i += 1) {
                if (mutations[i].attributeName === 'data-theme') {
                    var nextTheme = readTheme();
                    if (nextTheme !== system.theme) {
                        setTheme(nextTheme);
                    }
                    break;
                }
            }
        });

        system.observer.observe(document.documentElement, {
            attributes: true,
            attributeFilter: ['data-theme']
        });
    }

    function start() {
        ensureCanvas();
        resizeCanvas();
        bindObservers();
        setTheme(readTheme());
        window.addEventListener('resize', resizeCanvas);
        if (system.rafId) {
            window.cancelAnimationFrame(system.rafId);
        }
        system.lastFrame = 0;
        system.rafId = window.requestAnimationFrame(frame);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', start, { once: true });
    } else {
        start();
    }
}());
