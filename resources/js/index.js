// Driver number-flow (https://github.com/barvian/number-flow).
//
// Registra o web component <number-flow> e inicializa todo elemento
// number-flow[data-value] renderizado pelo pacote:
//
// 1. Primeiro render: exibe 0 e, após data-delay (ms), anima até data-value.
// 2. Atualizações (Livewire poll/morph): quando o morph troca o atributo
//    data-value, o MutationObserver anima do valor atual para o novo.
// 3. Elementos que chegam depois (paginação, modais, wire:navigate) são
//    inicializados pelo observer global de novos nós.
//
// O bundle é gerado por bin/build.js em resources/dist/filament-odometer-easy.js.
import 'number-flow'

const parseAttribute = (el, attribute) => {
    const raw = el.getAttribute(attribute)

    if (raw === null || raw === '') {
        return null
    }

    try {
        return JSON.parse(raw)
    } catch {
        return raw
    }
}

const configure = (el) => {
    const locales = parseAttribute(el, 'data-locales')
    const format = parseAttribute(el, 'data-format')

    if (locales !== null) {
        el.locales = locales
    }

    if (format !== null && typeof format === 'object') {
        el.format = format
    }

    applyTiming(el)
}

// data-duration (ms) controla a velocidade da animação. Sem o atributo,
// valem os timings padrão do number-flow (~900ms com easing de mola).
const applyTiming = (el) => {
    const duration = Number(el.getAttribute('data-duration'))

    if (!Number.isFinite(duration) || duration <= 0) {
        return
    }

    const easing = 'cubic-bezier(0.16, 1, 0.3, 1)'

    el.transformTiming = { duration, easing }
    el.spinTiming = { duration, easing }
    el.opacityTiming = { duration: Math.min(duration / 2, 450), easing: 'ease-out' }
}

const targetValue = (el) => {
    const value = Number(el.getAttribute('data-value'))

    return Number.isFinite(value) ? value : 0
}

const initialDelay = (el) => {
    const delay = Number(el.getAttribute('data-delay'))

    return Number.isFinite(delay) && delay >= 0 ? delay : 0
}

const boot = (el) => {
    if (el.__odometerEasyBooted) {
        return
    }

    el.__odometerEasyBooted = true

    configure(el)

    // O primeiro update() do number-flow define o estado inicial sem animar:
    // partimos de 0 para que o próximo update() anime até o valor real.
    el.update(0)

    setTimeout(() => el.update(targetValue(el)), initialDelay(el))

    // Livewire morph preserva o elemento e troca só os atributos: qualquer
    // mudança em data-value re-anima do valor atual para o novo.
    new MutationObserver(() => {
        configure(el)
        el.update(targetValue(el))
    }).observe(el, {
        attributes: true,
        attributeFilter: ['data-value', 'data-format', 'data-locales', 'data-duration'],
    })
}

const scan = (root) => {
    if (root.matches?.('number-flow[data-value]')) {
        boot(root)
    }

    root.querySelectorAll?.('number-flow[data-value]').forEach(boot)
}

new MutationObserver((mutations) => {
    for (const mutation of mutations) {
        mutation.addedNodes.forEach((node) => {
            if (node.nodeType === Node.ELEMENT_NODE) {
                scan(node)
            }
        })
    }
}).observe(document.documentElement, {
    childList: true,
    subtree: true,
})

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => scan(document.documentElement))
} else {
    scan(document.documentElement)
}
