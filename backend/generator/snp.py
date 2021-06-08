import requests
from bs4 import BeautifulSoup

COUNT = 10000

snps = set()

counter = 0

while len(snps) < COUNT:
    r = requests.get('https://randomus.ru/name?type=0&sex=10&count=100')
    soup = BeautifulSoup(r.text)
    data = soup.findAll('div', {'class': 'tags copy_button'})
    data = [str(x['data-clipboard-text']) for x in data]
    [snps.add(x+'\n') for x in data]
    counter = len(snps)
    print('Progress: ' + str(counter))

with open('snps.txt', 'w', encoding='UTF-8') as f:
    f.writelines(snps)
