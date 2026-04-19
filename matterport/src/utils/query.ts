// Parse url
export const objectFromQuery = (url: string) => {
  const regex = /[#&?]([^=]+)=([^#&?]+)/g;
  url = url || window.location.href;
  const object: { [param: string]: string } = {};
  let matches;
  // regex.exec returns new matches on each
  // call when we use /g like above
  while ((matches = regex.exec(url)) !== null) {
    object[matches[1]] = decodeURIComponent(matches[2]);
  }
  return object;
};
