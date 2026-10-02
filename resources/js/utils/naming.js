import { slug } from './string'

export const PREFIX = 'tallkit'

export function eventName(name) {
  return `${PREFIX}:${name}`
}

export function storageKey(...parts) {
  return parts.every((part) => part !== null && part !== undefined && part !== '')
    ? [PREFIX, ...parts].join('.')
    : null
}

export function dataKey(name) {
  return `data-${PREFIX}-${name}`
}

export function dataSelector(name, value) {
  return value
    ? `[${dataKey(name)}="${CSS.escape(String(value))}"]`
    : `[${dataKey(name)}]`
}

export function queryData(root, name, value) {
  return root?.querySelector(dataSelector(name, value)) ?? null
}

export function queryAllData(root, name, value) {
  return Array.from(root?.querySelectorAll(dataSelector(name, value)) ?? [])
}

export function generateId(prefix, name, suffix) {
  return slug([
    PREFIX,
    prefix,
    name ?? Math.random().toString(36).slice(2, 9),
    suffix,
  ].filter(Boolean).join('-')) ?? ''
}
